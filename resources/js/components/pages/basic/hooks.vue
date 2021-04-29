<template>
  <div>
    <h1>Hook tutorial</h1>
    <p>My name is: {{ name }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: "dinhkhoi",
    };
  },
  computed: {
    propertyComputed() {
      console.log("I change when this.property changes.");
      return this.name;
    },
  },

  created() {
    this.name = "Example property update.";
    console.log(
      "propertyComputed will update, as this.property is now reactive."
    );
  },
};
</script>

<style>
</style>