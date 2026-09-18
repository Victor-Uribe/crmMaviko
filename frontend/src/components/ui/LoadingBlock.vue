<template><div class="loading"><span></span><span></span><span></span></div></template>
<style scoped>
.loading { min-height:180px; display:flex; align-items:center; justify-content:center; gap:6px; }
.loading span { width:7px; height:7px; border-radius:50%; background:var(--primary); animation:b 1s infinite ease-in-out; }
.loading span:nth-child(2){animation-delay:.12s}.loading span:nth-child(3){animation-delay:.24s}@keyframes b{0%,80%,100%{opacity:.3;transform:scale(.8)}40%{opacity:1;transform:scale(1)}}
</style>
