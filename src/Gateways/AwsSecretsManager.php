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
     * Retrieve secrets from AWS Secrets Manager.
     */
    public function getSecrets(?string $region, ?string $secretName = null, ?string $tagKey = null, ?string $tagValue = null): Collection
    {
        $client = new SecretsManagerClient([
            'version' => self::AWS_VERSION,
            'region' => $region,
        ]);

        try {
            ['SecretList' => $secretList] = $client->listSecrets([
                'Filters' => $this->getFilters($secretName, $tagKey, $tagValue),
                'MaxResults' => self::MAX_SECRETS,
            ]);
        } catch (Exception $exception) {
            logger()->alert('Error retrieving secrets from AWS Secrets Manager', ['exception' => $exception]);
            throw $exception;
        }

        return collect($secretList)
            ->flatMap(function ($secret) use ($client) {
                try {
                    ['SecretString' => $secretString] = $client->getSecretValue([
                        'SecretId' => $secret['ARN'],
                    ]);

                    return json_decode($secretString, true, self::JSON_DECODE_DEPTH, JSON_THROW_ON_ERROR);
                } catch (JsonException $exception) {
                    logger()->alert('Error decoding response from AWS Secrets Manager', ['exception' => $exception]);
                    throw $exception;
                } catch (Exception $exception) {
                    logger()->alert('Error retrieving secret value from AWS Secrets Manager', ['exception' => $exception]);
                    throw $exception;
                }
            });
    }

    /**
     * Get filters to be used with the listSecrets() function.
     */
    private function getFilters(?string $secretName, ?string $tagKey, ?string $tagValue): array
    {
        $filters = [];

        if (! empty($secretName)) {
            $filters[] = [
                'Key' => 'name',
                'Values' => [$secretName],
            ];
        }

        if (! empty($tagKey) && ! empty($tagValue)) {
            array_push($filters,
                [
                    'Key' => 'tag-key',
                    'Values' => [$tagKey],
                ],
                [
                    'Key' => 'tag-value',
                    'Values' => [$tagValue],
                ]);
        }

        return $filters;
    }
}
