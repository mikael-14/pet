const colors = require("tailwindcss/colors");

module.exports = {
    content: ["./resources/**/*.blade.php", "./vendor/filament/**/*.blade.php"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary:  {
                  '50': '#ECB7B4',
                  '100': '#EAB1AE',
                  '200': '#E7A5A1',
                  '300': '#E49995',
                  '400': '#E08985',
                  '500': '#DC7A75',
                  '600': '#D76660',
                  '700': '#D04B44',
                  '800': '#BF3830',
                  '900': '#932B25',
                  '950': '#7E2520'
                },
                danger: colors.rose,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
