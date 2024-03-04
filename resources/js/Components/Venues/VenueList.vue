<template>

  <div class="venue-list">
    <h2 class="text-2xl font-bold my-4">Venue List</h2>
    <!-- <button @click="searchVenues">Search</button>
    <button @click="filterVenues">Filter</button> -->

    <div class="my-4">
      <button @click="toggleUncomplete" class="px-4 py-2 bg-blue-500 text-white rounded">Uncomplete</button>
      <div v-if="showUncomplete" class="mt-2">
        <VenueCard v-for="venue in uncompleteVenues" :key="venue.id" :venue="venue" />
      </div>
    </div>

    <div class="my-4">
      <button @click="toggleComplete" class="px-4 py-2 bg-green-500 text-white rounded">Complete</button>
      <div v-if="showComplete" class="mt-2">
        <VenueCard v-for="venue in completeVenues" :key="venue.id" :venue="venue" />
      </div>
    </div>
  </div>
</template>

<script>
import VenueCard from '/resources/js/Components/Venues/VenueCard.vue';

export default {
  components: {
    VenueCard
  },
  props: {
    venues: Array
  },
  data() {
    return {
      showUncomplete: false,
      showComplete: false
    };
  },
  computed: {
    uncompleteVenues() {
      return this.venues.filter(venue => !venue.complete);
    },
    completeVenues() {
      return this.venues.filter(venue => venue.complete);
    }
  },
  methods: {
    toggleUncomplete() {
      this.showUncomplete = !this.showUncomplete;
    },
    toggleComplete() {
      this.showComplete = !this.showComplete;
    },
    // selectVenue(venue) {
    //   this.$emit('venue-selected', venue);
    // },
    searchVenues() {
      // Implement search functionality
    },
    filterVenues() {
      // Implement filter functionality
    }
  }
};
</script>
