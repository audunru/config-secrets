<?php

namespace audunru\ConfigSecrets\Gateways;

use Aws\SecretsManager\SecretsManagerClient;
use Exception;
use Illuminate\Support\Collection;
use JsonException;

class AwsSecretsManager
{
    /**
     * Version to utilize.
     */
    protected const AWS_VERSION = '2017-10-17';

    /**
     * Maximum number of secrets to retrieve.
     */
    protected const MAX_SECRETS = 100;

    /**
     * Secrets manager supports secrets stored in JSON format as key/value pairs.
     */
    protected const JSON_DECODE_DEPTH = 2;

    /**
     * Only secret with this name will be retrieved.
     */
    protected string $secretName;

    /**
     * Only secrets tagged with this key will be retrieved.
     */
    protected string $tagKey;

    /**
     * Only secrets tagged with this value will be retrieved.
     */
    protected string $tagValue;

    /**
     * AWS client.
     */
    protected SecretsManagerClient $client;

    public function __construct()
    {
        $this->secretName = config('config-secrets.providers.aws.secret-name', '');
        $this->tagKey = config('config-secrets.providers.aws.tag-key', '');
        $this->tagValue = config('config-secrets.providers.aws.tag-value', '');
        $this->client = new SecretsManagerClient([
            'version' => self::AWS_VERSION,
            'region'  => config('config-secrets.providers.aws.region'),
        ]);
    }

    /**
     * Retrieve secrets from AWS Secrets Manager.
     */
    public function getSecrets(): Collection
    {
        try {
            ['SecretList' => $secretList] = $this->client->listSecrets([
                'Filters'    => $this->getFilters(),
                'MaxResults' => self::MAX_SECRETS,
            ]);
        } catch (Exception $exception) {
            logger()->alert('Error retrieving secrets from AWS Secrets Manager', ['exception', $exception]);
            throw $exception;
        }

        return collect($secretList)
            ->flatMap(function ($secret) {
                try {
                    ['SecretString' => $secretString] = $this->client->getSecretValue([
                        'SecretId' => $secret['ARN'],
                    ]);

                    return json_decode($secretString, true, self::JSON_DECODE_DEPTH, JSON_THROW_ON_ERROR);
                } catch (JsonException $exception) {
                    logger()->alert('Error decoding response from AWS Secrets Manager', ['exception', $exception]);
                    throw $exception;
                } catch (Exception $exception) {
                    logger()->alert('Error retrieving secret value from AWS Secrets Manager', ['exception', $exception]);
                    throw $exception;
                }
            });
    }

    /**
     * Get filters to be used with the listSecrets() function.
     */
    private function getFilters(): array
    {
        $filters = [];

        if (! empty($this->secretName)) {
            $filters[] = [
                'Key'    => 'name',
                'Values' => [$this->secretName],
            ];
        }

        if (! empty($this->tagKey) && ! empty($this->tagValue)) {
            array_push($filters,
                [
                    'Key'    => 'tag-key',
                    'Values' => [$this->tagKey],
                ],
                [
                    'Key'    => 'tag-value',
                    'Values' => [$this->tagValue],
                ]);
        }

        return $filters;
    }
}
