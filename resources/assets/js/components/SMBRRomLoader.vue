<template>
    <div>
        <div v-if="!loading" id="rom-select" class="">
            <b-card title="Select ROM File" style="max-width: 40rem;">
                <p class="card-text">
                    <p>
                        <b-form-file v-model="romFile" accept=".nes" @change="loadBlob" :state="Boolean(romFile)" placeholder="Select ROM File..."></b-form-file>
                        <ol>
                            <li>Select your ROM file and load it into the browser. The randomizer will tell you if the selected ROM file is usable. The recommended ROM is named
                                <strong>Super Mario Bros. (JU) [!].nes</strong>
                            </li>
                            <li>Select which options to use for the randomization</li>
                            <li>Click Generate</li>
                            <li>Click Save</li>
                        </ol>
                    </p>
            </b-card>
        </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
      romFile: null,
      baseRomHash: "",
      baseRomSize: 0,
      loadedRomHash: ""
    };
  },

  created() {
    axios.get("/settings/base_rom").then(response => {
      this.baseRomHash = response.data.rom_hash;
      this.baseRomSize = response.data.rom_size;
    });
  },

  mounted() {
    EventBus.$on("noBlob", this.noBlob);
    EventBus.$on("loadBlob", this.loadBlob);
  },

  methods: {
    noBlob() {
      this.loading = false;
    },

    loadBlob(change) {
      this.loading = true;
      let blob = change.target.files[0];

      new ROM(blob, rom => {
        this.loadedRomHash = rom.getMD5();
        if (this.loadedRomHash != this.baseRomHash) {
          this.$emit("error", "ROM File Not Recognized");
          this.loading = false;
          return;
        } else {
          localforage.setItem("rom", rom.getOriginalArrayBuffer());
          this.$emit("update", rom, this.baseRomHash);
          this.loading = false;
        }
      });
    }
  }
};
</script>
