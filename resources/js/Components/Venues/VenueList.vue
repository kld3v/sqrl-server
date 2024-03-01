<template>
  <div class="venue-list">
    <button @click="searchVenues">Search</button>
    <button @click="filterVenues">Filter</button>
    <ul>
      <li v-for="venue in venues" :key="venue.id" @click="selectVenue(venue)">
        {{ venue.name }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    venues: Array
  },
  methods: {
    selectVenue(venue) {
      this.$emit('venue-selected', venue);
    },
    searchVenues() {
      // Implement search functionality
    },
    filterVenues() {
      // Implement filter functionality
    }
  }
};
</script>
