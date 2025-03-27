// .storybook/main.js
const config = {
  stories: [
    '../stories/**/*.stories.mdx',
    '../stories/**/*.stories.@(js|jsx|ts|tsx)',
    '../components/**/*.stories.mdx',
    '../components/**/*.stories.@(json|yml)',
  ],
  // ...jj
  addons: [
    '@storybook/addon-links',
    '@storybook/addon-essentials',
    '@lullabot/storybook-drupal-addon', // <---
  ],
  framework: {
    name: '@storybook/server-webpack5',
    options: {},
  },
  docs: {
    autodocs: 'tags',
  },
};

export default config;
















