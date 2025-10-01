<?php

namespace DroxNL\Rdw;

use GuzzleHttp\Client;
use DroxNL\Rdw\Exceptions\InvalidLicenseException;
use DroxNL\Rdw\Exceptions\UnknownLicenseDataException;
use DroxNL\Rdw\Exceptions\UnreachableEndpointException;

class RdwApi
{
    protected $client;

    protected $endpoints = [
        'info'              => 'm9d7-ebf2.json',
        'fuel'              => '8ys7-d773.json',
        'bodywork'          => 'vezc-m2t6.json',
        'bodywork_specific' => 'jhie-znh9.json',
        'vehicle_class'     => 'kmfi-hrps.json',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://opendata.rdw.nl/resource/',
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * @param string $license
     * @param array $types
     * @return Result
     * @throws InvalidLicenseException
     * @throws UnreachableEndpointException
     * @throws UnknownLicenseDataException
     */
    public function find(string $license, array $types): Result
    {
        $license = $this->formatLicense($license);

        if (strlen($license) !== 6) {
            throw new InvalidLicenseException($license);
        }

        $fuelTypes = [];
        $data = [];
        foreach ($types as $type) {
            if (isset($this->endpoints[$type]) === false || $type === 'transmission') {
                continue;
            }

            try {
                $response = ($this->client->get("{$this->endpoints[$type]}?kenteken={$license}"));
                $statusCode = $response->getStatusCode() ?? 404;

                if ($statusCode !== 200) {
                    throw new UnknownLicenseDataException('license', $license);
                }

                $responseBody = (string)$response->getBody();
                $data = array_merge($data, $this->formatResponse($responseBody)[0] ?? []);

                if ($type === 'fuel') {
                    $fuelTypes = $this->formatResponse($responseBody);
                }
            } catch (UnknownLicenseDataException $exception) {
                throw $exception;
            } catch(\Throwable $e) {
                throw new UnreachableEndpointException($type);
            }
        }

        if (in_array('transmission', $types) && isset($data['typegoedkeuringsnummer'])) {
            $approvedKey = $data['typegoedkeuringsnummer'];

            if (strpos($approvedKey, '/') !== false) {
                $approvedKeySplitted = explode('/', $approvedKey);
                $yearSplitted = substr($approvedKeySplitted[0], 5, 7);
                $approvedKeyFiltered = substr($approvedKeySplitted[0], 0, 3) . $yearSplitted . '/' . $approvedKeySplitted[1];
                $variant = $data['variant'];

                try {
                    $response = ($this->client->get("{$this->endpoints['transmission']}?eu_type_goedkeuringssleutel={$approvedKeyFiltered}&eeg_variantcode={$variant}"));
                    $statusCode = $response->getStatusCode() ?? 404;

                    if ($statusCode !== 200) {
                        throw new UnknownLicenseDataException('license', $license);
                    }

                    $responseBody = (string)$response->getBody();
                    $data = array_merge($data, $this->formatResponse($responseBody)[0] ?? []);
                } catch (UnknownLicenseDataException $exception) {
                    throw $exception;
                } catch(\Throwable $e) {
                    throw new UnreachableEndpointException($type);
                }
            }
        }

        return new Result($data, $fuelTypes);
    }

    /**
     * @param string $license
     * @return string
     */
    protected function formatLicense(string $license): string
    {
        $license = preg_replace("/[^a-zA-Z0-9]+/", "", str_replace('-', '', $license));

        return strtoupper($license);
    }

    /**
     * @param $data
     * @return array
     */
    protected function formatResponse($data): array
    {
        return json_decode($data, true);
    }
}
