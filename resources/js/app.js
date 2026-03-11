
// Instruckt — visual feedback toolbar (only loaded in dev)
if (import.meta.env.DEV) {
    import('instruckt').then(({ Instruckt }) => new Instruckt({ endpoint: '/instruckt', adapters: ['livewire', 'blade'] }));
}
