<template>
  <div class="venue-page flex">
    <div class="w-1/4">
      <VenueList :venues="venues" @venue-selected="handleVenueSelected" />
    </div>
    <div class="w-1/4" v-if="selectedVenue">
      <VenueDetails :venue="selectedVenue" />
    </div>
    <div class="w-1/2">
      <MapDisplay :venue="selectedVenue" />
    </div>
  </div>
</template>

<script>
import VenueList from "/resources/js/Components/Venues/VenueList.vue";
import VenueDetails from "/resources/js/Components/Venues/VenueDetails.vue";
import MapDisplay from "/resources/js/Components/Venues/MapDisplay.vue";

export default {
  props: {
    venues: Array
  },
  components: {
    VenueList,
    VenueDetails,
    MapDisplay
  },
  data() {
    return {
      selectedVenue: null
    };
  },
  mounted() {
    if (this.venues && this.venues.length > 0) {
      this.selectedVenue = this.venues[0];
    }
  },
  methods: {
    handleVenueSelected(venue) {
      this.selectedVenue = venue;
    }
  }
};
</script>
