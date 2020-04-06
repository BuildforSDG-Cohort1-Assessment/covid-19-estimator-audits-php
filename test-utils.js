import axios from 'axios';

const FIELDS = {
  'ch-1': ['currentlyInfected', 'infectionsByRequestedTime'],
  'ch-2': ['severeCasesByRequestedTime', 'hospitalBedsByRequestedTime'],
  'ch-3': [
    'casesForICUByRequestedTime',
    'casesForVentilatorsByRequestedTime',
    'dollarsInFlight'
  ]
};

export const getImpactDataStructure = (challenge) => {
  const challengeFields = !challenge
    ? Object.values(FIELDS).flat()
    : FIELDS[challenge];
  return challengeFields.reduce(
    (map, field) => {
      map.impact[field] = expect.any(Number);
      map.severeImpact[field] = expect.any(Number);
      return map;
    },
    {
      impact: {},
      severeImpact: {}
    }
  );
};

export const valueOnFields = (input, output, challenge) => {
  const challengeFields = !challenge
    ? Object.values(FIELDS).flat()
    : FIELDS[challenge];
  return challengeFields.reduce((table, f) => {
    table.push([input.impact[f], output.impact[f]]);
    table.push([input.severeImpact[f], output.severeImpact[f]]);
    return table;
  }, []);
};

export const mockEstimationFor = async (periodType) => {
  const apiBase = 'https://us-central1-buildforsdg.cloudfunctions.net/api';
  return axios.get(
    `${apiBase}/gen/covid-19-scenario/${periodType.toLowerCase()}`
  );
};