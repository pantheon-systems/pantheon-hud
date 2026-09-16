import { createBdd } from 'playwright-bdd';
import { test as cmsBddTest, expect } from 'cms-bdd';
const { Given, When, Then } = createBdd(cmsBddTest);

// The admin session comes from storageState, set up once by the setup project.
// This step documents the precondition in Gherkin; it has nothing to do.
Given('I log in as an admin', async () => {});

Given('I go to {string}', async ({ page }, url: string) => {
  await page.goto(url, { waitUntil: 'load' });
});

Then('the {string} element should contain {string}', async ({ page }, selector: string, text: string) => {
  await expect(page.locator(selector)).toContainText(text);
});
