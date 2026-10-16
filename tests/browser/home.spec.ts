import { expect, test } from '@playwright/test';

test('homepage shows the offer and the primary action', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Your business. Reimagined.');
    await expect(page.getByRole('link', { name: "Let's talk" })).toBeVisible();
    await expect(page.getByText('Demonstration').first()).toBeVisible();
});

test('mobile navigation opens', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/');
    await page.getByRole('button', { name: 'Menu' }).click();
    await expect(page.locator('#mobile-nav').getByRole('link', { name: 'Services', exact: true })).toBeVisible();
});

test('a quote request is saved and a staff user can find it', async ({ page }) => {
    const name = `Playwright ${Date.now()}`;
    await page.goto('/quote');
    await page.getByRole('textbox', { name: 'Name', exact: true }).fill(name);
    await page.getByRole('textbox', { name: 'Email', exact: true }).fill('playwright@example.test');
    await page.getByLabel('What do you need?').fill('A website for a local business.');
    await page.getByRole('button', { name: 'Send' }).click();
    await expect(page.getByRole('status')).toContainText('recorded');

    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email', exact: true }).fill('owner@surework.test');
    await page.getByLabel('Password').fill('local-only-change-me');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page).toHaveURL(/\/admin/);
    await page.goto(`/admin/leads?search=${encodeURIComponent(name)}`);
    await expect(page.getByRole('link', { name })).toBeVisible();
});
