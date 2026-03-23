export function createBuilderApi(config) {
  return {
    config,

    // Save and media actions will be connected in the next implementation phase.
    async saveDraft() {
      return Promise.resolve(null);
    },

    async publish() {
      return Promise.resolve(null);
    },
  };
}
