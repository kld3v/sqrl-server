<template>
  <div class="venue-details" v-if="venue">
    <h2 class="text-2xl font-bold my-4">Venue Details</h2>
    <div v-for="field in orderedFields" :key="field" class="flex justify-between items-center mb-4">
      <label v-if="field !== 'area'" class="font-bold capitalize">{{ field }}</label>
      <div 
        v-if="field !== 'area'" 
        class="editableField shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
        contenteditable="false"
        @dblclick="makeEditable($event)"
        @blur="saveChanges($event, field)">
        {{ venue[field] }}
      </div>

      <div v-if="field === 'area'" class="w-full">
        <label class="block font-bold capitalize">{{ field }}</label>
        <div v-for="(areaItem, index) in venue.area" :key="index" class="flex items-center mb-2">
          <input type="text" v-model="venue.area[index]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
          <button @click="removeArea(index)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Delete
          </button>
        </div>
        <!-- <button @click="addArea" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Add Area
        </button> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    venue: Object
  },
  data() {
    return {
      orderedFields: ['id', 'company', 'chain', 'url_id', 'tel', 'address', 'postcode', 'google_maps', 'area','midpoint', 'status', 'complete'],
    };
  },
  methods: {
    // addArea() {
    //   this.venue.area.push('');
    // },
    removeArea(index) {
      this.venue.area.splice(index, 1);
    },
    makeEditable(event) {
      event.target.contentEditable = true;
      event.target.focus();
    },
    saveChanges(event, field) {
      this.venue[field] = event.target.innerText;
      event.target.contentEditable = false;
    }
  }
};
</script>

<style>
.editableField {
  cursor: pointer;
  min-width: 200px; /* Adjust based on your layout */
}
.editableField:focus {
  background-color: #f0f0f0; /* Light grey background when editable */
}
</style>
