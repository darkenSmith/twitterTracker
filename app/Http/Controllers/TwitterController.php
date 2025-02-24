<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Resources\twitterResource;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TwitterController extends Controller
{
    public function index(Request $request){

        $request->validate(['q' => 'required']);
        // Retrieve the search query parameter, default to "crypto"
        $query = $request->input('q', 'crypto');
        // Get the Bearer Token from configuration

        $bearerToken = config('services.twitter.bearer_token');

        // Initialize Guzzle client with the Twitter API v2 base URI
        $client = new Client([
            'base_uri' => 'https://api.twitter.com/2/',
        ]);

        try {
            // Call the "recent search" endpoint
            $response = $client->request('GET', 'tweets/search/recent', [
                'headers' => [
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'query' => [
                    'query'        => $query,
                    'max_results'  => 10,
                    // Request extra tweet fields like creation date (optional)
                    'tweet.fields' => 'created_at,author_id',
                ],
            ]);
            // Decode the JSON response
            $body = $response->getBody();
            $data = json_decode($body, true);

            // The API returns tweets in the "data" key
            $tweets = $data['data'] ?? [];
            return response()->json(['tweets' => $tweets]);
        } catch (\Exception $e) {
            \Log::error("Twitter API error: " . $e->getMessage());
            $error  = "Error fetching tweets: " . $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }
}
