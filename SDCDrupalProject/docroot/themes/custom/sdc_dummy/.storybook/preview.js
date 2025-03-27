/** @type { import('@storybook/web-components').Preview } */
const preview = {
  globals: {
    drupalTheme: 'olivero',
    supportedDrupalThemes: {
      umami: {title: 'Umami'},
      claro: {title: 'Claro'},
    },
  },
  parameters: {
    server: {
      // Replace this with your Drupal site URL, or an environment variable.
      url: 'https://sdcdrupalproject.ddev.site/',
    },
    actions: { argTypesRegex: "^on[A-Z].*" },
    controls: {
      matchers: {
       color: /(background|color)$/i,
       date: /Date$/i,
      },
    },
  },
};

export default preview;