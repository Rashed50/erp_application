<template>
    <div class="card p-3">
        <canvas ref="depositCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip, Filler } from 'chart.js'

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip, Filler)

const props = defineProps({
    labels: Array,
    data: Array,
    title: String
})

const depositCanvas = ref(null)
let depositChart = null

function renderChart() {
    if (!depositCanvas.value) return
    if (depositChart) depositChart.destroy()

    depositChart = new Chart(depositCanvas.value.getContext('2d'), {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: 'Deposits',
                    data: props.data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true }, title: { display: true, text: props.title } },
            scales: { y: { title: { display: true, text: 'Number of Deposits' } } }
        }
    })
}

onMounted(() => {
    renderChart()
})

watch([() => props.labels, () => props.data], () => {
    renderChart()
})
</script>
