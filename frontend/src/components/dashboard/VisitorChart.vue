<template>
    <div class="card p-3">
        <canvas ref="visitorCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip } from 'chart.js'

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip)

const props = defineProps({
    labels: Array,
    data: Array,
    days: Number
})

const visitorCanvas = ref(null)
let visitorChart = null

function renderChart() {
    if (!visitorCanvas.value) return
    if (visitorChart) visitorChart.destroy()

    visitorChart = new Chart(visitorCanvas.value.getContext('2d'), {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: `Last ${props.days} Days Customers`,
                    data: props.data,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(0,0,0,0)',
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            stacked: false,
            plugins: { legend: { display: true }, title: { display: false } },
            scales: { y: { title: { display: true, text: 'Customers' } } }
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
