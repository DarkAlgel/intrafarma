import { test, expect } from '@playwright/test';

test('Navbar oculta para convidados e visível após login', async ({ page }) => {
  await page.goto('/login');
  const sidebar = page.locator('.sidebar');
  await expect(sidebar).toHaveClass(/sidebar-hidden/);

  await page.goto('/_test/login');
  await page.goto('/dashboard');
  await expect(sidebar).not.toHaveClass(/sidebar-hidden/);
});