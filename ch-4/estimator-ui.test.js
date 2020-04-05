/**
 * @jest-environment node
 */

import puppeteer from "puppeteer";
import propertiesReader from "properties-reader";

let page;
let browser;
let frontendURL;

const cases = [
  ["button", "data-go-estimate"],
  ["population", "data-population"],
  ["periodType", "data-period-type"],
  ["timeToElapse", "data-time-to-elapse"],
  ["reportedCases", "data-reported-cases"],
  ["totalHospitalBeds", "data-total-hospital-beds"],
];

describe("on-covid-19 >> Challenge-4", () => {
  beforeAll(async () => {
    const properties = propertiesReader("./app.properties");
    frontendURL = properties.get("frontend.url");

    browser = await puppeteer.launch();
    page = await browser.newPage();
  });

  afterAll(async () => {
    await browser.close();
  });

  test.each(cases)(
    "UI has %s input with a %s attribute",
    async (field, attr) => {
      await page.goto(frontendURL, { waitUntil: "domcontentloaded" });

      let query = `input[${attr}]`;
      if (attr === "data-go-estimate" || attr === "data-period-type") {
        query = `[${attr}]`;
      }

      const input = await page.$(query);
      expect(input).toBeTruthy();

      if (field === "periodType") {
        query = `${query} option`;
        const getOptionValues = (options) =>
          options.map((option) => (option.value || "").toLowerCase());
        const values = await page.$$eval(query, getOptionValues);

        expect(values).toHaveLength(3);
        expect(values).toEqual(["days", "weeks", "months"]);
      }
    },
    15000
  );
});
