import preset from './tailwind.config.preset'

export default {
    presets: [preset],
    content: ['./packages/**/*.blade.php'],
}

module.exports = {
    safelist: [
        'bg-red-100',
        'bg-red-50',
    ],
    // ...
}