/**
 * @jest-environment node
 */

import axios from 'axios';
import propertiesReader from 'properties-reader';
import {
  valueOnFields,
  mockEstimationFor,
  getImpactDataStructure
} from '../test-utils.js';

let api;
const formats = [['json', 'application/json'], ['xml', 'application/xml']];
const periodTypes = [['days'], ['weeks'], ['months']];

describe('on-covid-19 >> Challenge-5', () => {
  beforeAll(async () => {
    const properties = propertiesReader('./app.properties');
    api = properties.get('backend.rest');
  });

  test('app.properties file contains REST API', async () => {
    expect(api).toBeTruthy();
    expect(api).not.toBe('https://jsonplaceholder.typicode.com/todos');
  });

  test.each(periodTypes)(
    'REST API estimates correctly, in %s',
    async (periodType) => {
      const estimation = await mockEstimationFor(periodType);
      const { data, estimate } = estimation.data;
      const estimated = await axios.post(api, data);
      const { data: outputData, impact, severeImpact } = estimated.data;

      expect(outputData).toBeTruthy();
      expect(impact).toBeTruthy();
      expect(severeImpact).toBeTruthy();
      const result = { data: outputData, impact, severeImpact };
      expect(result).toMatchObject(getImpactDataStructure());

      const values = valueOnFields(result, estimate);
      values.forEach(([produced, expected]) => {
        expect(produced).toBe(expected);
      });
    }
  );

  test.each(formats)('API handles request for %s format', async (fmt, cntType) => {
    const estimation = await mockEstimationFor('weeks');
    const { data } = estimation.data;
    const { headers } = await axios.post(`${api}/${fmt}`, data);

    const status = headers['content-type'].indexOf(cntType);
    expect(status).toBeGreaterThanOrEqual(0);
  });

  test.skip('API provides loags at /logs', () => {});
});