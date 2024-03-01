<template>
  <div class="venue-page">
    <VenueList :venues="venues" @venue-selected="handleVenueSelected" />
    <VenueDetails :venue="selectedVenue" />
    <MapDisplay :venue="selectedVenue" />
  </div>
</template>

<script>
import VenueList from "/resources/js/Components/Venues/VenueList.vue";
import VenueDetails from "/resources/js/Components/Venues/VenueDetails.vue";
import MapDisplay from "/resources/js/Components/Venues/MapDisplay.vue";

export default {
  props: {
    venues: Array // Expecting this to be passed from the server
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
  methods: {
    handleVenueSelected(venue) {
      this.selectedVenue = venue;
    }
  }
};
</script>
