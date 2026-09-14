<template>
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <!-- Info text -->
        <div class="pagination-info">
            মোট {{ total.toLocaleString() }} রেকর্ডের মধ্যে
            {{ from.toLocaleString() }} থেকে {{ to.toLocaleString() }} দেখানো হচ্ছে
        </div>

        <div class="d-flex align-items-center gap-5">
            <!-- Per page -->
            <div class="d-flex align-items-center gap-3">
                রেকর্ড দেখাও :
                <select class="form-select form-select-sm w-auto" :value="perPage"
                    @change="changePerPage($event.target.value)">
                    <option v-for="size in perPageOptions" :key="size" :value="size">
                        {{ size }}
                    </option>
                </select>
            </div>



            <!-- Pagination buttons -->
            <ul class="pagination mb-0">
                <!-- Previous -->
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="change(currentPage - 1)">
                        « পূর্ববর্তী
                    </a>
                </li>

                <!-- Page numbers -->
                <li v-for="page in pages" :key="page" class="page-item"
                    :class="{ active: page === currentPage, disabled: page === '...' }">
                    <a v-if="page !== '...'" class="page-link" href="#" @click.prevent="change(page)">
                        {{ page }}
                    </a>
                    <span v-else class="page-link">…</span>
                </li>

                <!-- Next -->
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <a class="page-link" href="#" @click.prevent="change(currentPage + 1)">
                        পরবর্তী »
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>


<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: { type: Number, required: true },
    perPage: { type: Number, required: true },
    total: { type: Number, required: true },
    lastPage: { type: Number, required: true },
    maxVisible: { type: Number, default: 5 },
})

const perPageOptions = [15, 25, 50, 100, 1000, 2000, 3000, 5000, 'All'];

const emit = defineEmits(['page-change', 'per-page-change'])

const from = computed(() => {
    if (props.perPage === 'All') {
        return props.total === 0 ? 0 : 1;
    }
    return props.total === 0 ? 0 : (props.currentPage - 1) * props.perPage + 1;
})

const to = computed(() => {
    if (props.perPage === 'All') {
        return props.total;
    }
    return Math.min(props.currentPage * props.perPage, props.total);
})

const pages = computed(() => {
    if (props.perPage === 'All') {
        return [1];
    }

    const pages = []
    const half = Math.floor(props.maxVisible / 2)

    let start = Math.max(1, props.currentPage - half)
    let end = Math.min(props.lastPage, start + props.maxVisible - 1)

    if (end - start + 1 < props.maxVisible) {
        start = Math.max(1, end - props.maxVisible + 1)
    }

    if (start > 1) {
        pages.push(1)
        if (start > 2) pages.push('...')
    }

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    if (end < props.lastPage) {
        if (end < props.lastPage - 1) pages.push('...')
        pages.push(props.lastPage)
    }

    return pages
})

const change = (page) => {
    if (props.perPage === 'All') return;
    if (page < 1 || page > props.lastPage || page === props.currentPage) return
    emit('page-change', page)
}

const changePerPage = (value) => {
    emit('per-page-change', value === 'All' ? 'All' : Number(value))
}
</script>
