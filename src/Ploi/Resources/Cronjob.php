<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Cronjob extends Resource
{
    private $server;

    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/crontabs');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this;
    }

    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(string $command, string $frequency, string $user = 'ploi'): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'command' => $command,
                'frequency' => $frequency,
                'user' => $user,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        // Set the id of the cronjob
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response;
    }

    public function delete(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}
