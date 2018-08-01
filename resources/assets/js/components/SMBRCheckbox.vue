<template>
  <div>
    <b-form-group :label="label">
      <b-form-select @input="onInput" v-model="value" :options="options">
      </b-form-select>
    </b-form-group>
  </div>
</template>

<script>
export default {
  props: {
    label: { default: "Label" },
    options: { default: () => [] },
    storageKey: { default: null },
    selected: { default: null }
  },

  data() {
    return {
      value: ""
    };
  },

  created() {
    if (this.storageKey != null) {
      // If we have a storage key, look for option stored in localforage
      localforage.getItem(this.storageKey).then(
        function(localvalue) {
          if (localvalue != null) {
            this.value = localvalue;
          }
        }.bind(this)
      );
    }
  },

  methods: {
    onInput() {
      if (this.value != null) {
        localforage.setItem(this.storageKey, this.value);
      }
      this.$emit("input", this.value);
    },

    changeSelected(value) {}
  }
};
</script>