import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/browser',
    use: { baseURL: 'http://127.0.0.1:8123' },
    webServer: {
        command: 'bash scripts/serve-preview.sh',
        url: 'http://127.0.0.1:8123/up',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
