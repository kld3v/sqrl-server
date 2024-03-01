<template>
  <div class="venue-details" v-if="venue">
    <div v-for="(value, key) in venue.details" :key="key">
      <label>{{ key }}</label>
      <span>{{ value }}</span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    venue: Object
  }
};
</script>

<style>
/* Add styles for venue details component */
</style>
