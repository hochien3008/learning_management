<template>
    <div :class="colorClass">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            <g opacity="0.4">
                <path
                    d="M9.62 17.3005C10.7908 17.3005 11.74 16.3513 11.74 15.1805C11.74 14.0096 10.7908 13.0605 9.62 13.0605C8.44915 13.0605 7.5 14.0096 7.5 15.1805C7.5 16.3513 8.44915 17.3005 9.62 17.3005Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M11.7402 15.1795V7.76953" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path
                    d="M13.1302 6.7701L15.4702 7.55006C16.0402 7.74006 16.5002 8.38006 16.5002 8.98006V9.60005C16.5002 10.4101 15.8702 10.8601 15.1102 10.6001L12.7703 9.82008C12.2003 9.63008 11.7402 8.99009 11.7402 8.39009V7.7701C11.7402 6.9701 12.3602 6.5101 13.1302 6.7701Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </g>
        </svg>


    </div>


</template>


<script setup>

const props = defineProps({
    colorClass: {
        type: String,
        default: '#24262D'
    },
});
</script>
