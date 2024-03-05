<template>
  <div class="map-display" ref="mapDisplay">
    <h2 class="text-2xl font-bold my-4">Map</h2>
    <!-- Here you would integrate a map display, such as Google Maps or Leaflet -->
  </div>
</template>

<script>
export default {
  props: {
    venue: Object
  },
  data() {
    return {
      areaPolygon: null, // Define the polygon here
    };
  },
  mounted() {
    this.loadGoogleMapsScript().then(() => {
      this.initMap();
    }).catch((error) => {
      console.error('Error loading Google Maps:', error);
    });
  },
  methods: {
    loadGoogleMapsScript() {
      return new Promise((resolve, reject) => {
        // Check if the Google Maps API script is already loaded
        if (typeof google !== 'undefined' && google.maps) {
          resolve();
          return;
        }

        // Create script element to include Google Maps API
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDBpdy_2P5bjZiE1rXmbFf1qeUs5tMLA-c`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        script.onload = () => resolve();
        script.onerror = (error) => reject(error);
      });
    },
    initMap() {
      const mapCenter = { lat: this.venue.midpoint[1], lng: this.venue.midpoint[0] };
      const map = new google.maps.Map(this.$refs.mapDisplay, {
        zoom: 19,
        center: mapCenter,
      });

      // Attach the polygon object to this
      this.areaPolygon = new google.maps.Polygon({
        paths: this.venue.area.map(coord => ({ lat: coord[1], lng: coord[0] })),
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
      });

      this.areaPolygon.setMap(map);

      // Marker creation code
      this.areaPolygon.getPaths().forEach((path, pIndex) => {
        path.forEach((latLng, index) => {
          const marker = new google.maps.Marker({
            position: latLng,
            map: map,
            draggable: true,
          });

          marker.addListener('dragend', () => {
            const pos = marker.getPosition();
            this.updateAreaPoint(pIndex, index, { lat: pos.lat(), lng: pos.lng() });
            this.updatePolygonPath(); // Ensure this is called here to immediately reflect changes
          });
        });
      });
    },

    // Method to update area point in parent component
    updateAreaPoint(pathIndex, pointIndex, newPosition) {
      // Update the venue area with the new position
      this.venue.area[pointIndex] = [newPosition.lng, newPosition.lat];
      this.$emit('update:venue', this.venue); // Notify parent component about the update

      // After updating the area, also update the polygon's path
      this.updatePolygonPath();
    },

    updatePolygonPath() {
      const newPath = this.venue.area.map(coord => ({ lat: coord[1], lng: coord[0] }));
      this.areaPolygon.setPaths(newPath); // Directly update the polygon paths
    },

  }
};
</script>

<style>
/* Add styles for map display component */
.map-display {
  height: 500px; /* Example height, adjust as needed */
}
</style>
