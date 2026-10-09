<script setup>
import { computed, nextTick, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Reveal from '@/components/ui/Reveal.vue'

const page = usePage()
const navDepartemens = computed(() => page.props.navDepartemens ?? [])

const input = ref('')
const outputRef = ref(null)
const lines = ref([
    { html: '<span class="text-cyan-300">BEM SAINTEK OS v2.6</span>' },
    { html: 'Type <span class="text-white">help</span> to see available commands.' },
    { html: '<span class="text-white/20">─────────────────────────────────────</span>' },
])

const chips = ['help', 'departments', 'vision', 'impact', 'clear']

function departmentsHtml() {
    if (navDepartemens.value.length) {
        const rows = navDepartemens.value
            .map((d, i) => `<div>${String(i + 1).padStart(2, '0')} — ${d.nama}</div>`)
            .join('')
        return `<div class="text-cyan-300">${navDepartemens.value.length} ACTIVE NODES</div>${rows}`
    }
    return `<div class="text-cyan-300">8 ACTIVE NODES</div><div>01 — RISTEK</div><div>02 — PSDM</div><div>03 — KOMINFO</div><div>04 — SOSMAS</div><div>05 — EKRAF</div><div>06 — ADVOKASI</div><div>07 — MINBAK</div><div>08 — EKSTERNAL</div>`
}

const commands = computed(() => ({
    help: `<div class="text-white">Available commands:</div><div><span class="text-cyan-300">departments</span> — explore our ecosystem</div><div><span class="text-cyan-300">vision</span> — read our direction</div><div><span class="text-cyan-300">impact</span> — view movement statistics</div><div><span class="text-cyan-300">about</span> — who we are</div><div><span class="text-cyan-300">clear</span> — clear terminal</div>`,
    departments: departmentsHtml(),
    vision: `<div class="text-violet-300">VISION</div><div class="mt-1">Membangun ekosistem mahasiswa yang inklusif, adaptif, inovatif dan berdampak.</div>`,
    impact: `<div class="text-green-300">IMPACT REPORT</div><div>Projects........ 32</div><div>Partners........ 24</div><div>Students........ 1,204</div><div>Impact.......... 98.7%</div>`,
    about: `<div class="text-cyan-300">ABOUT SAINTEK</div><div>A student movement built around science, technology, collaboration and impact.</div>`,
}))

async function scrollDown() {
    await nextTick()
    if (outputRef.value) outputRef.value.scrollTop = outputRef.value.scrollHeight
}

function runCommand(raw) {
    const cmd = raw.trim().toLowerCase()
    if (!cmd) return
    lines.value.push({ html: `<span class="text-white/20">visitor@bem:~$ ${cmd}</span>` })
    if (cmd === 'clear') {
        lines.value = []
    } else if (commands.value[cmd]) {
        lines.value.push({ html: commands.value[cmd] })
    } else {
        lines.value.push({ html: `<span class="text-red-300">command not found: ${cmd}</span><div class="text-white/20">Type "help" for available commands.</div>` })
    }
    scrollDown()
}

function submit() {
    runCommand(input.value)
    input.value = ''
}
</script>

<template>
    <section class="relative py-36">
        <div class="max-w-5xl mx-auto px-5 lg:px-8">
            <Reveal variant="up">
                <div class="text-center">
                    <div class="font-mono text-[10px] text-violet-300 tracking-[.3em]">08 / INTERFACE</div>
                    <h2 class="font-display text-5xl md:text-7xl font-bold tracking-[-.06em] mt-5 text-white">
                        TALK TO <span class="text-white/20">SAINTEK.</span>
                    </h2>
                    <p class="text-white/30 mt-6">Ketik command untuk menjelajahi sistem.</p>
                </div>
            </Reveal>

            <Reveal variant="up" :delay="100">
                <div class="terminal rounded-[2rem] overflow-hidden mt-14">
                    <div class="h-12 px-5 border-b border-white/5 flex items-center justify-between">
                        <div class="flex gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        </div>
                        <span class="font-mono text-[9px] text-white/20">visitor@bem-saintek:~</span>
                    </div>
                    <div ref="outputRef" class="h-[270px] overflow-y-auto p-6 font-mono text-xs text-white/50 space-y-2">
                        <div v-for="(l, i) in lines" :key="i" v-html="l.html"></div>
                    </div>
                    <form class="border-t border-white/5 p-5 flex items-center gap-3" @submit.prevent="submit">
                        <span class="font-mono text-cyan-300 text-xs shrink-0">visitor@bem:~$</span>
                        <input
                            v-model="input"
                            autocomplete="off"
                            spellcheck="false"
                            class="flex-1 bg-transparent outline-none font-mono text-xs text-white placeholder:text-white/20"
                            placeholder="type a command..."
                        />
                    </form>
                </div>
            </Reveal>

            <div class="flex flex-wrap justify-center gap-2 mt-5">
                <button
                    v-for="c in chips"
                    :key="c"
                    class="px-3 py-1.5 rounded-full border border-white/10 text-[10px] font-mono text-white/35 hover:text-cyan-300 transition cursor-pointer"
                    @click="runCommand(c)"
                >
                    {{ c }}
                </button>
            </div>
        </div>
    </section>
</template>
