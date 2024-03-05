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
        zoom: 15,
        center: mapCenter,
      });

      const areaPolygon = new google.maps.Polygon({
        paths: this.venue.area.map(coord => ({ lat: coord[1], lng: coord[0] })),
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        editable: true, // Make polygon edges draggable
      });

      areaPolygon.setMap(map);

      // For each vertex, create a draggable marker and update area coordinates on drag
      areaPolygon.getPaths().forEach((path, pIndex) => {
        path.forEach((latLng, index) => {
          const marker = new google.maps.Marker({
            position: latLng,
            map: map,
            draggable: true,
          });

          marker.addListener('dragend', () => {
            const pos = marker.getPosition();
            this.updateAreaPoint(pIndex, index, { lat: pos.lat(), lng: pos.lng() });
          });
        });
      });
    },

    // Method to update area point in parent component
    updateAreaPoint(pathIndex, pointIndex, newPosition) {
      this.venue.area[pointIndex] = [newPosition.lng, newPosition.lat];
      this.$emit('update:venue', this.venue); // Use this if venue is a prop passed down from parent
    },

  }
};
</script>

<style>
/* Add styles for map display component */
.map-display {
  height: 400px; /* Example height, adjust as needed */
}
</style>
