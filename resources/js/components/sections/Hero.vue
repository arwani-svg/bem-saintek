<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import Reveal from '@/components/ui/Reveal.vue'

const canvasRef = ref(null)
const glowRef = ref(null)
const terminalRef = ref(null)

let raf = 0
let particles = []
let particleMouse = { x: null, y: null, radius: 150 }

function resizeCanvas(canvas) {
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
}

function initParticles() {
    const canvas = canvasRef.value
    if (!canvas) return
    const ctx = canvas.getContext('2d')
    resizeCanvas(canvas)

    const count = window.innerWidth < 700 ? 65 : 125
    particles = Array.from({ length: count }, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random() - 0.5) * 0.35,
        vy: (Math.random() - 0.5) * 0.35,
        size: Math.random() * 1.6 + 0.5,
    }))

    function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height)
        for (const p of particles) {
            p.x += p.vx
            p.y += p.vy
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1

            if (particleMouse.x !== null) {
                const dx = p.x - particleMouse.x
                const dy = p.y - particleMouse.y
                const dist = Math.sqrt(dx * dx + dy * dy) || 1
                if (dist < particleMouse.radius) {
                    const force = (particleMouse.radius - dist) / particleMouse.radius
                    p.x += (dx / dist) * force * 0.7
                    p.y += (dy / dist) * force * 0.7
                }
            }

            ctx.beginPath()
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2)
            ctx.fillStyle = 'rgba(103,232,249,.65)'
            ctx.fill()
        }

        for (let a = 0; a < particles.length; a++) {
            for (let b = a + 1; b < particles.length; b++) {
                const dx = particles[a].x - particles[b].x
                const dy = particles[a].y - particles[b].y
                const d = Math.sqrt(dx * dx + dy * dy)
                if (d < 125) {
                    ctx.beginPath()
                    ctx.strokeStyle = `rgba(34,211,238,${(1 - d / 125) * 0.13})`
                    ctx.lineWidth = 0.6
                    ctx.moveTo(particles[a].x, particles[a].y)
                    ctx.lineTo(particles[b].x, particles[b].y)
                    ctx.stroke()
                }
            }
        }

        raf = requestAnimationFrame(loop)
    }
    loop()
}

function onMouseMove(e) {
    particleMouse.x = e.clientX
    particleMouse.y = e.clientY
    if (glowRef.value) {
        glowRef.value.style.left = e.clientX + 'px'
        glowRef.value.style.top = e.clientY + 'px'
    }
    if (terminalRef.value && window.innerWidth >= 1000) {
        const x = (e.clientX / window.innerWidth - 0.5) * 2
        const y = (e.clientY / window.innerHeight - 0.5) * 2
        terminalRef.value.style.transform = `translate(${x * 5}px, ${y * 5}px)`
    }
}

function onResize() {
    if (canvasRef.value) resizeCanvas(canvasRef.value)
}

onMounted(() => {
    initParticles()
    window.addEventListener('mousemove', onMouseMove)
    window.addEventListener('resize', onResize)
})

onUnmounted(() => {
    cancelAnimationFrame(raf)
    window.removeEventListener('mousemove', onMouseMove)
    window.removeEventListener('resize', onResize)
})
</script>

<template>
    <section id="home" class="relative min-h-screen overflow-hidden flex items-center pt-20">
        <canvas ref="canvasRef" class="absolute inset-0 w-full h-full opacity-75"></canvas>
        <div
            ref="glowRef"
            class="absolute w-[600px] h-[600px] rounded-full pointer-events-none blur-[10px] -translate-x-1/2 -translate-y-1/2"
            style="background: radial-gradient(circle, rgba(34,211,238,.09), rgba(59,130,246,.03) 35%, transparent 70%)"
        ></div>
        <div class="absolute inset-0 grid-bg opacity-50"></div>

        <div class="absolute top-28 left-5 lg:left-10 z-10">
            <div class="flex items-center gap-3 font-mono text-[9px] text-white/30">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 shadow-[0_0_10px_#4ade80]"></span>
                SYSTEM ONLINE <span class="text-white/10">/</span> SAINTEK.OS
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-5 lg:px-8 w-full py-16">
            <div class="grid lg:grid-cols-[1.25fr_.75fr] gap-14 items-center">
                <div>
                    <div class="flex items-center gap-3 mb-8">
                        <span class="w-10 h-px bg-cyan-300"></span>
                        <span class="font-mono text-[10px] text-cyan-300 tracking-[.3em]">BEM SAINTEK / 2026</span>
                    </div>

                    <h1 class="hero-title font-display font-bold uppercase text-white flex flex-col gap-2">
                        <span class="hero-word" style="animation-delay: .2s">Welcome To</span>
                        <span class="hero-word gradient-text" style="animation-delay: .34s">Badan Eksekutif Mahasiswa</span>
                        <span class="hero-word gradient-text" style="animation-delay: .48s">Fakultas Sains dan Teknologi</span>
                        <span class="hero-word text-white/20" style="animation-delay: .50s">Universitas Ibrahimy</span>
                    </h1>

                    <div class="mt-10 max-w-xl">
                        <div class="hero-line h-px bg-gradient-to-r from-cyan-300/70 via-blue-500/40 to-transparent"></div>
                        <p class="mt-6 text-white/40 text-sm md:text-base leading-relaxed">
                            Ketika sains bertemu keberanian. Ketika teknologi bertemu kemanusiaan.
                            Ketika mahasiswa memutuskan untuk tidak hanya menyaksikan perubahan — tetapi membangunnya.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="#about"
                            class="group inline-flex items-center gap-3 px-6 py-3.5 rounded-full bg-cyan-300 text-black text-sm font-bold hover:bg-white transition"
                        >
                            Explore Movement
                            <span class="w-7 h-7 rounded-full bg-black/10 flex items-center justify-center group-hover:rotate-45 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M7 7h10v10" />
                                </svg>
                            </span>
                        </a>
                        <a
                            href="#departments"
                            class="inline-flex items-center gap-3 px-6 py-3.5 rounded-full border border-white/10 bg-white/[.025] text-white/60 text-sm hover:text-white hover:bg-white/[.06] transition"
                        >
                            Explore Departments
                        </a>
                    </div>
                </div>

                <Reveal variant="up" :delay="200">
                    <div ref="terminalRef" class="terminal rounded-[1.7rem] overflow-hidden">
                        <div class="h-11 px-5 border-b border-white/5 flex items-center justify-between">
                            <div class="flex gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-400/80"></span>
                                <span class="w-2 h-2 rounded-full bg-yellow-400/80"></span>
                                <span class="w-2 h-2 rounded-full bg-green-400/80"></span>
                            </div>
                            <span class="font-mono text-[9px] text-white/20">mission_control</span>
                        </div>
                        <div class="p-6 font-mono text-xs">
                            <div class="text-white/20 mb-5">BEM SAINTEK TERMINAL v2.6</div>
                            <div class="terminal-line text-cyan-300 mb-3">$ initialize_movement()</div>
                            <div class="terminal-line text-white/45 mb-3" style="animation-delay:.3s">→ loading students...</div>
                            <div class="terminal-line text-white/45 mb-3" style="animation-delay:.55s">→ connecting departments...</div>
                            <div class="terminal-line text-white/45 mb-3" style="animation-delay:.8s">→ syncing ideas...</div>
                            <div class="terminal-line text-green-300 mb-6" style="animation-delay:1.05s">✓ movement initialized</div>

                            <div class="border-t border-white/5 pt-5">
                                <div class="flex justify-between mb-2">
                                    <span class="text-white/25">IMPACT</span>
                                    <span class="text-cyan-300">98.7%</span>
                                </div>
                                <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full w-[98%] bg-gradient-to-r from-cyan-300 to-blue-500"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mt-5 font-sans">
                                    <div class="p-3 rounded-xl bg-white/[.025] border border-white/5">
                                        <div class="text-white/20 text-[9px] font-mono">DEPARTMENTS</div>
                                        <div class="text-lg mt-1 text-white">08</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white/[.025] border border-white/5">
                                        <div class="text-white/20 text-[9px] font-mono">PROJECTS</div>
                                        <div class="text-lg mt-1 text-white">32</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white/[.025] border border-white/5">
                                        <div class="text-white/20 text-[9px] font-mono">STUDENTS</div>
                                        <div class="text-lg mt-1 text-white">1.2K</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white/[.025] border border-white/5">
                                        <div class="text-white/20 text-[9px] font-mono">STATUS</div>
                                        <div class="text-lg mt-1 text-green-300">LIVE</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 text-white/20">$ <span class="text-white/40">create_impact</span><span class="blink">_</span></div>
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>

        <div class="absolute bottom-7 left-1/2 -translate-x-1/2">
            <div class="flex flex-col items-center gap-3">
                <span class="font-mono text-[8px] tracking-[.4em] text-white/20">SCROLL</span>
                <div class="w-px h-12 bg-gradient-to-b from-cyan-300/60 to-transparent"></div>
            </div>
        </div>
    </section>
</template>
