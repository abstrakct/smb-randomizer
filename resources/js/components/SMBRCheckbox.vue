<template>
  <div>
    <b-form-group>
      <b-form-checkbox @input="onInput" v-model="value" :value="checkedValue" :unchecked-value="uncheckedValue">
        {{ label }}
      </b-form-checkbox>
    </b-form-group>
  </div>
</template>

<script>
export default {
  props: {
    label: { default: "Label" },
    options: { default: () => [] },
    storageKey: { default: null },
    selected: { default: null },
    checkedValue: { default: null },
    uncheckedValue: { default: null }
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