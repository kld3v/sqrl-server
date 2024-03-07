<template>
  <div class="map-wrapper">
    <div class="overlay-content">
      <h2 class="text-2xl font-bold my-4">Map</h2>
    </div>
    <div class="map-display" ref="mapDisplay">
      <!-- Google Maps or Leaflet map will be injected here -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    venue: Object,
  },
  data() {
    return {
      areaPolygon: null, // Define the polygon here
    };
  },
  mounted() {
    this.loadGoogleMapsScript()
      .then(() => {
        this.initMap();
      })
      .catch((error) => {
        console.error("Error loading Google Maps:", error);
      });
  },
  methods: {
    loadGoogleMapsScript() {
      return new Promise((resolve, reject) => {
        // Check if the Google Maps API script is already loaded
        if (typeof google !== "undefined" && google.maps) {
          resolve();
          return;
        }

        // Create script element to include Google Maps API
        const script = document.createElement("script");
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDBpdy_2P5bjZiE1rXmbFf1qeUs5tMLA-c`; // Replace YOUR_API_KEY with your actual API key
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        script.onload = () => resolve();
        script.onerror = (error) => reject(error);
      });
    },
    initMap() {
      const mapCenter = {
        lat: this.venue.midpoint[0],
        lng: this.venue.midpoint[1],
      };
      const map = new google.maps.Map(this.$refs.mapDisplay, {
        zoom: 19,
        center: mapCenter,
      });

      this.areaPolygon = new google.maps.Polygon({
        paths: this.venue.area.map((coord) => ({
          lat: coord[0],
          lng: coord[1],
        })),
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        editable: true, // This allows the polygon to be directly edited
        draggable: true, // This allows the polygon to be moved
      });

      this.areaPolygon.setMap(map);

      // Update venue area when polygon is edited
      google.maps.event.addListener(this.areaPolygon.getPath(), 'set_at', this.updateVenueArea);
      google.maps.event.addListener(this.areaPolygon.getPath(), 'insert_at', this.updateVenueArea);
    },
    updateVenueArea() {
      const path = this.areaPolygon.getPath();
      const newArea = [];
      path.forEach((latLng) => {
        const lat = latLng.lat();
        const lng = latLng.lng();
        newArea.push([lat, lng]);
      });
      this.venue.area = newArea;
      this.$emit("update:venue", this.venue);
    },
  },
};
</script>

<style>
.map-wrapper {
  position: relative;
  height: 500px;
}

.map-display {
  height: 100%;
}

.overlay-content {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1000;
  width: 100%;
  text-align: center;
  padding-top: 10px;
}
</style>
