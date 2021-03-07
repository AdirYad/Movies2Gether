module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    prefix: 'tw-',
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                'base-white': 'var(--base-white)',
            }
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
