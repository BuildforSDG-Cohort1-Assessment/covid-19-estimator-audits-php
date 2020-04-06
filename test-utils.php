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
      $f,
      $input["impact"][$f],
      $output["impact"][$f],
    ]);

    array_push($table, [
      $f,
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
  
  $data = '{
    "data":{
       "region":{
          "name":"Africa",
          "avgAge":19.7,
          "avgDailyIncomeInUSD":5,
          "avgDailyIncomePopulation":0.66
       },
       "periodType":"days",
       "timeToElapse":74,
       "reportedCases":1716,
       "population":16700793,
       "totalHospitalBeds":2802813
    },
    "estimate":{
       "impact":{
          "currentlyInfected":171601,
          "infectionsByRequestedTime":287897026560,
          "severeCasesByRequestedTime":43184553984,
          "hospitalBedsByRequestedTime":-43183572999.45,
          "casesForICUByRequestedTime":14394851328,
          "casesForVentilatorsByRequestedTime":5757940531.2,
          "dollarsInFlight":70304453885952
       },
       "severeImpact":{
          "currentlyInfected":85800,
          "infectionsByRequestedTime":1439485132800,
          "severeCasesByRequestedTime":215922769920,
          "hospitalBedsByRequestedTime":-215921788935.45,
          "casesForICUByRequestedTime":71974256640,
          "casesForVentilatorsByRequestedTime":28789702656,
          "dollarsInFlight":351522269429760
       }
    }
 }';
  $response_object = null;
  try {
    $response = $client->request('GET', 'gen/covid-19-scenario/' . strtolower($periodType));
    // $response_object = json_decode($response->getBody());
    $response_object = json_decode($data);
  } catch (GuzzleHttp\Exception\RequestException $e) {
    throw new Exception($e);
  }

  return $response_object;
}
