<template>
  <div>
    <h1>We can have some data here: {{ views }}</h1>
    <button @click="updateCounter(1)">Increase</button>
    <button @click="updateCounter(-1)">Decrease</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      views: 0,
    };
  },
  methods: {
    updateCounter(number) {
      this.views += number;
    },
  },
  created() {
      // call the server to get views of this blog post...
      this.views = 5;
  },
};
</script>

<style>
</style>