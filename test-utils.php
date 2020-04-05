<?php
require 'vendor/autoload.php';

$fields = [
  "ch-1" => [
    "currentlyInfected",
    "infectionsByRequestedTime"
  ],
  "ch-2" => [
    "severeCasesByRequestedTime",
    "hospitalBedsByRequestedTime"
  ],
  "ch-3" => [
    "casesForICUByRequestedTime",
    "casesForVentilatorsByRequestedTime",
    'dollarsInFlight'
  ]
];

function getImpactDataStructure($challenge)
{
  return [
    "impact" => [],
    "severeImpact" => [],
  ];
}


function valueOnFields($input, $output, $challenge)
{
  $fields = $GLOBALS['fields'];

  $values = array_reduce($fields[$challenge], function ($table, $f) use ($input, $output) {
    array_push($table, [
      $input["impact"][$f],
      $output["impact"][$f],
    ]);

    array_push($table, [
      $input["severeImpact"][$f],
      $output["severeImpact"][$f],
    ]);

    return $table;
  }, []);

  return $values;
}

function convertEstimatesToArray($response_object)
{
  $data = (array) $response_object->data;
  $data["region"] = (array) $data["region"];

  $estimate = (array) $response_object->estimate;
  $estimate["impact"] = (array) $estimate["impact"];
  $estimate["severeImpact"] = (array) $estimate["severeImpact"];

  return [
    "data" => $data,
    "estimate" => $estimate,
  ];
}

function mockEstimatorFor($periodType)
{
  $client = new GuzzleHttp\Client(['base_uri' => 'https://us-central1-buildforsdg.cloudfunctions.net/api/']);
  $response = $client->request('GET', 'gen/covid-19-scenario/' . strtolower($periodType));

  $response_object = null;
  try {
    $response = $client->request('GET', 'gen/covid-19-scenario/days');
    $response_object = json_decode($response);
  } catch (GuzzleHttp\Exception\RequestException $e) {
    throw new Exception($e);
  }

  return $response_object;
}
