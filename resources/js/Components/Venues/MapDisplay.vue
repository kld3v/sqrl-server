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
      map: null, // Store the map object
      areaPolygon: null, // Polygon representation of the venue area
      markers: [], // Markers for each point in the area
    };
  },
  watch: {
    'venue.area': {
      handler(newValue) {
        this.updatePolygonPath();
        this.refreshMarkers();
      },
      deep: true,
    },
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

      this.map.addListener('click', (mapsMouseEvent) => {
        // Get lat and lng from the click event
        const clickedLat = mapsMouseEvent.latLng.lat();
        const clickedLng = mapsMouseEvent.latLng.lng();

        // Add the new point to the venue area and emit an update event
        this.venue.area.push([clickedLng, clickedLat]);
        this.$emit('update:venue', this.venue);

        // Update the polygon and markers
        this.updatePolygonPath();
        this.refreshMarkers();
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

    refreshMarkers() {
      // Remove existing markers
      this.markers.forEach(marker => marker.setMap(null));
      this.markers = [];

      // Add new markers based on the updated venue.area
      this.venue.area.forEach((coord, index) => {
        const position = { lat: coord[1], lng: coord[0] };
        const marker = new google.maps.Marker({
          position,
          map: this.map,
          draggable: true,
        });

        marker.addListener('dragend', () => {
          const pos = marker.getPosition();
          this.updateAreaPoint(0, index, { lat: pos.lat(), lng: pos.lng() });
        });

        this.markers.push(marker);
      });
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
