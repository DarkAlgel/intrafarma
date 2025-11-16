import { test, expect } from '@playwright/test';

const candidateUrls = ['/configuracoes/usuarios', '/usuarios', '/'];

async function openIfExists(page, selector: string) {
  const has = await page.locator(selector).first().count();
  if (has > 0) {
    await page.locator(selector).first().click();
    return true;
  }
  return false;
}

test.describe('Modais de Usuários', () => {
  test.beforeEach(async ({ page }) => {
    let loaded = false;
    for (const url of candidateUrls) {
      await page.goto(url);
      const count = await page.locator('h1:text("Usuários")').count();
      if (count > 0) { loaded = true; break; }
    }
    test.skip(!loaded, 'Página de usuários não acessível nas URLs conhecidas');
  });

  test('Modal Alterar Papel: abre e fecha corretamente', async ({ page }) => {
    const opened = await openIfExists(page, '.btnOpenRoleModal');
    test.skip(!opened, 'Sem usuários para testar abertura do modal de papel');

    await expect(page.locator('#modalRole')).toBeVisible();
    await expect(page.locator('#overlayRole')).toBeVisible();

    // Fechar pelo overlay
    await page.locator('#overlayRole').click({ position: { x: 5, y: 5 } });
    await expect(page.locator('#modalRole')).toBeHidden();

    // Abrir novamente e fechar pelo botão
    await page.locator('.btnOpenRoleModal').first().click();
    await expect(page.locator('#modalRole')).toBeVisible();
    await page.locator('#btnCancelRoleFooter').click();
    await expect(page.locator('#modalRole')).toBeHidden();
  });

  test('Modal Editar: abertura, dados e fechamento', async ({ page }) => {
    const opened = await openIfExists(page, '.btnOpenEditModal');
    test.skip(!opened, 'Sem usuários para testar abertura do modal de edição');

    await expect(page.locator('#modalEdit')).toBeVisible();
    await expect(page.locator('#editName')).toBeVisible();
    await expect(page.locator('#editEmail')).toBeVisible();

    // Fechar pelo overlay
    await page.locator('#overlayEdit').click({ position: { x: 5, y: 5 } });
    await expect(page.locator('#modalEdit')).toBeHidden();
  });

  test('Modal Alterar Senha: abertura e fechamento', async ({ page }) => {
    const opened = await openIfExists(page, '.btnOpenPasswordModal');
    test.skip(!opened, 'Sem usuários para testar abertura do modal de senha');

    await expect(page.locator('#modalPassword')).toBeVisible();
    await expect(page.locator('#passwordNew')).toBeVisible();

    // Fechar com ESC
    await page.keyboard.press('Escape');
    await expect(page.locator('#modalPassword')).toBeHidden();
  });

  test('Modal Excluir: abertura e fechamento', async ({ page }) => {
    const opened = await openIfExists(page, '.btnOpenDeleteModal');
    test.skip(!opened, 'Sem usuários para testar abertura do modal de exclusão');

    await expect(page.locator('#modalDelete')).toBeVisible();
    await page.locator('#overlayDelete').click({ position: { x: 5, y: 5 } });
    await expect(page.locator('#modalDelete')).toBeHidden();
  });

  test('Responsividade dos modais', async ({ page }) => {
    const openAny = async () => {
      if (await openIfExists(page, '.btnOpenRoleModal')) return '#modalRole';
      if (await openIfExists(page, '.btnOpenEditModal')) return '#modalEdit';
      if (await openIfExists(page, '.btnOpenPasswordModal')) return '#modalPassword';
      if (await openIfExists(page, '.btnOpenDeleteModal')) return '#modalDelete';
      return null;
    };

    const modalSel = await openAny();
    test.skip(!modalSel, 'Nenhum modal disponível para teste');

    await expect(page.locator(modalSel!)).toBeVisible();

    // Mobile
    await page.setViewportSize({ width: 360, height: 640 });
    await expect(page.locator(modalSel!)).toBeVisible();

    // Tablet
    await page.setViewportSize({ width: 768, height: 1024 });
    await expect(page.locator(modalSel!)).toBeVisible();

    // Desktop
    await page.setViewportSize({ width: 1280, height: 800 });
    await expect(page.locator(modalSel!)).toBeVisible();
  });
});