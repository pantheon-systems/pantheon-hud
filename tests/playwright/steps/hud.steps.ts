import { createBdd } from 'playwright-bdd';
import { test as cmsBddTest, expect } from 'cms-bdd';
const { Given, Then } = createBdd(cmsBddTest);

// The admin session comes from storageState, set up once by the setup project.
// This step documents the precondition in Gherkin; it has nothing to do.
Given('I log in as an admin', async () => {});

// cms-bdd's "the {string} element should contain {string}" compares rendered
// text, which cannot see attributes such as an icon's src. This one checks the
// element's HTML, matching Behat's Mink elementContains semantics.
Then('the {string} element should contain the HTML {string}', async ({ page }, selector: string, html: string) => {
  await expect.poll(() => page.locator(selector).first().innerHTML()).toContain(html);
});
