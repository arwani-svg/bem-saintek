import { ref, watch, onMounted } from 'vue'

const isDark = ref(false)

export function useDarkMode() {
    onMounted(() => {
        const saved = localStorage.getItem('darkMode')
        if (saved !== null) {
            isDark.value = saved === 'true'
        } else {
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
        }
        applyTheme()
    })

    watch(isDark, () => {
        applyTheme()
        localStorage.setItem('darkMode', isDark.value)
    })

    function applyTheme() {
        document.documentElement.classList.toggle('dark', isDark.value)
    }

    function toggle() {
        isDark.value = !isDark.value
    }

    return { isDark, toggle }
}
