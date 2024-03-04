<template>
  <div
    @click="selectVenue"
    class="max-w-sm rounded overflow-hidden shadow-lg bg-white p-4"
  >
    <div class="font-bold text-xl mb-2">{{ venue.company }}</div>
    <p class="text-gray-700 text-base">{{ venue.chain }}</p>
    <p class="text-gray-700 text-base">{{ venue.id }}</p>
    <p class="text-gray-700 text-base">{{ venue.url.url }}</p>
  </div>
</template>

<script>
export default {
  props: {
    venue: Object,
  },
  methods: {
    selectVenue() {
      console.log('clicked')
      this.$emit("venue-selected", this.venue);
    },
  },
};
</script>