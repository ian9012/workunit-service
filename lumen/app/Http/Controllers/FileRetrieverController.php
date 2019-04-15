<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pythia\Request\GithubCredential;
use Pythia\Services\FileRetriever;

/**
 * Class FileRetrieverController
 *
 * @package App\Http\Controllers
 *
 * @author Ian Charles <solaiman.mansyur@gmail.com>
 */
class FileRetrieverController
{
    /**
     * File retriever service
     *
     * @var FileRetriever
     */
    private $fileRetriever;

    /**
     * FileRetrieverController constructor.
     *
     * @param FileRetriever $fileRetriever a service to retrieve files from VCS
     */
    public function __construct(FileRetriever $fileRetriever)
    {
        $this->fileRetriever = $fileRetriever;
    }

    /**
     * Retrieve file change based on commit sha
     *
     * @param Request $request request which expected to contain the VCS auth token
     * @param string $owner VCS username or organization name.
     * @param string $repo
     * @param string $commitHash
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(Request $request, string $owner, string $repo, string $commitHash): JsonResponse
    {
        $responseStatus = 200;

        if (empty($request->bearerToken())) {
            $deliverableResponse = [
                'errors' => [
                    'Invalid Request Header'
                ]
            ];
            $responseStatus = 401;

            return response()->json($deliverableResponse, $responseStatus);
        }

        $authToken = $request->bearerToken();

        $gitHubCredential = new GithubCredential($owner, $repo, $authToken);
        $deliverableResponse = $this->fileRetriever->request($gitHubCredential, $commitHash);

        if (isset($deliverableResponse['errors'])) {
            $responseStatus = 400;
        }

        return response()->json($deliverableResponse, $responseStatus);
    }
}
