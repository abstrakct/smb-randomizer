<template>
  <div id="smbr-main">
    <b-container class="fluid">
      <b-row>
        <b-col> </b-col>
        <b-col cols="11">
          <b-card :title="'Super Mario Bros. Randomizer v' + this.version" style="max-width: 150rem;">

            <b-alert dismissible fade :show="error" variant="danger">
              Error: {{ this.errorMessage }}
            </b-alert>

            <b-row>
              <b-col>
                <b-alert :show="baseRomLoaded" variant="success" class="m-3">
                  <center>
                    <h6 class="alert-heading">ROM file loaded OK!</h6>
                    <b-button @click="unloadRom">Unload ROM file</b-button>
                  </center>
                </b-alert>

              </b-col>
              <b-col>
                <b-alert dismissible :show="baseRomLoaded" variant="info" class="m-3">
                  <center>
                    <h6 class="alert-heading">This is some information</h6>
                    Let me tell you something...
                  </center>
                </b-alert>

              </b-col>
            </b-row>
            <smbr-rom-loader v-if="!baseRomLoaded" @update="updateRom" @error="onError"></smbr-rom-loader>

            <p> </p>

            <div v-if="baseRomLoaded && optionsLoaded">
              <b-card title="Options" style="max-width: 150rem;">
                <b-button @click="loadDefaults" variant="info">Load default options</b-button>
                <p></p>
                <b-row>
                  <b-col>
                    <!-- INPUT FOR SEED NUMBER -->
                    <b-form-group label="Level Randomization" label-for="olr">
                      <b-select id="olr" v-model="selectedOptions.shuffleLevels" :options="randomizerOptions.shuffleLevels">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="World Length" label-for="owl">
                      <b-select id="owl" v-model="selectedOptions.normalWorldLength" :options="randomizerOptions.normalWorldLength">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Pipe Transitions" label-for="opt">
                      <b-select id="opt" v-model="selectedOptions.pipeTransitions" :options="randomizerOptions.pipeTransitions">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Warp Zones" label-for="owz">
                      <b-select id="owz" v-model="selectedOptions.warpZones" :options="randomizerOptions.warpZones">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Blocks" label-for="obl">
                      <b-select id="obl" v-model="selectedOptions.blocks" :options="randomizerOptions.blocks">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Enemies" label-for="oen">
                      <b-select id="oen" v-model="selectedOptions.enemies" :options="randomizerOptions.enemies">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Bowser's Abilities" label-for="oba">
                      <b-select id="oba" v-model="selectedOptions.bowserAbilities" :options="randomizerOptions.bowserAbilities">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Bowser's Hitpoints" label-for="obh">
                      <b-select id="obh" v-model="selectedOptions.bowserHitpoints" :options="randomizerOptions.bowserHitpoints">
                      </b-select>
                    </b-form-group>
                  </b-col>
                  <b-col>
                    <b-form-group label="Mario Color Scheme" label-for="ocsm">
                      <b-select id="ocsm" v-model="selectedOptions.colorscheme.mario" :options="randomizerOptions.colorscheme.mario">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Luigi Color Scheme" label-for="ocsl">
                      <b-select id="ocsl" v-model="selectedOptions.colorscheme.luigi" :options="randomizerOptions.colorscheme.luigi">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Fire Mario/Luigi Color Scheme" label-for="ocsf">
                      <b-select id="ocsf" v-model="selectedOptions.colorscheme.fire" :options="randomizerOptions.colorscheme.fire">
                      </b-select>
                    </b-form-group>
                    <b-form-group label="Starting Lives" label-for="osl">
                      <b-select id="osl" v-model="selectedOptions.startingLives" :options="randomizerOptions.startingLives">
                      </b-select>
                    </b-form-group>
                    <p> </p>

                    <b-button variant="success" @click="generateSeed" class="w-100">Generate!</b-button>
                    <div v-if="rando.done">
                      <p></p>
                      <b-button variant="success" @click="saveRandomizedRom" class="w-100">Save
                        <strong>{{ rando.filename }}</strong>
                      </b-button>
                      <p></p>
                      <b-button variant="info" class="w-100" :href="rando.logfullpath">View log (contains spoilers!)</b-button>
                    </div>
                  </b-col>
                </b-row>
              </b-card>
            </div>

            <p class="card-text">

            </p>
          </b-card>
        </b-col>
        <b-col> </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
  props: ["version"],

  // TODO: Store selected options in localforage
  // TODO: Disable certain options when certain options are selected!
  data() {
    return {
      // base rom
      baseRom: null,
      baseRomLoaded: false,
      baseRomHash: "",
      baseRomFilename: "",
      // randomized rom
      rando: {
        fullpath: "",
        filename: "",
        logfullpath: "",
        base64data: "",
        jsondata: null,
        done: false
      },
      // error
      error: false,
      errorMessage: "",
      // options
      optionsLoaded: false,
      defaultLoaded: false,
      defaultOptions: null,
      randomizerOptions: null,
      selectedOptions: {
        colorscheme: {
          mario: "",
          luigi: "",
          fire: ""
        },
        shuffleLevels: "",
        normalWorldLength: "",
        pipeTransitions: "",
        enemies: "",
        blocks: "",
        bowserAbilities: "",
        bowserHitpoints: "",
        startingLives: "",
        warpZones: ""
      }
    };
  },

  created() {
    // Thanks Veetorp!

    // Get all available options from the backend
    axios.get("/randomizer/options").then(response => {
      this.randomizerOptions = response.data;
      this.optionsLoaded = true;
    });

    // Get the default option settings from the backend
    axios.get("/randomizer/options/default").then(response => {
      this.defaultOptions = response.data;
      this.defaultLoaded = true;
      this.selectedOptions = Object.assign({}, this.defaultOptions);
    });

    // Look for stored option settings in localforage

    // Look for stored ROM in localforage, and load it if found.
    localforage.getItem("rom").then(function(blob) {
      if (blob == null) {
        EventBus.$emit("noBlob");
        return;
      }
      EventBus.$emit("loadBlob", { target: { files: [new Blob([blob])] } });
    });

    EventBus.$on("update-baserom-filename", this.updateFilename);
    EventBus.$on("store-randomized-rom", this.storeRandomizedRom);
  },

  methods: {
    generateSeed() {
      axios
        .post("/randomizer/generate", {
          headers: { "content-type": "multipart/form-data" },
          rom: this.baseRom.getData(),
          romfilename: this.baseRomFilename,
          mario: this.selectedOptions.colorscheme.mario,
          luigi: this.selectedOptions.colorscheme.luigi,
          fire: this.selectedOptions.colorscheme.fire,
          shuffleLevels: this.selectedOptions.shuffleLevels,
          normalWorldLength: this.selectedOptions.normalWorldLength,
          pipeTransitions: this.selectedOptions.pipeTransitions,
          enemies: this.selectedOptions.enemies,
          blocks: this.selectedOptions.blocks,
          bowserAbilities: this.selectedOptions.bowserAbilities,
          bowserHitpoints: this.selectedOptions.bowserHitpoints,
          startingLives: this.selectedOptions.startingLives,
          warpZones: this.selectedOptions.warpZones
        })
        .then(response => {
          this.rando.fullpath = response.data.fullpath;
          this.rando.filename = response.data.filename;
          this.rando.logfullpath = response.data.logfullpath;
          this.rando.base64data = response.data.base64data;
          this.rando.jsondata = response.data.jsondata;
          this.rando.done = true;
        })
        .then(() => {
          EventBus.$emit("store-randomized-rom");
        })
        .catch(error => {
          if (error.response) {
            switch (error.response.status) {
              case 429:
                this.error = true;
                this.errorMessage = "Whoa, calm down there buddy!";
                break;
              case 500:
                this.error = true;
                this.errorMessage = "Unknown server error!";
                break;
              default:
                this.error = true;
                this.errorMessage =
                  "Generating seed failed! No one knows why :(";
                break;
            }
          }
        });
    },

    updateRom(rom, current_rom_hash) {
      if (!rom) {
        console.log(rom);
        return;
      }

      this.baseRom = rom;
      this.baseRomHash = current_rom_hash;
      this.baseRomLoaded = true;
      localforage.getItem("romfilename").then(
        function(value) {
          if (value != null) {
            this.baseRomFilename = value;
          }
        }.bind(this)
      );
      this.error = false;
    },

    unloadRom() {
      this.baseRomLoaded = false;
      localforage.removeItem("rom").then(function() {});
      localforage.removeItem("romfilename").then(function() {});
    },

    storeRandomizedRom() {
      // Decode base64 encoded randomized rom data and store in localforage
      var binary_string = Base64.decode(this.rando.base64data);
      var s = "";
      var data = [];

      for (var i = 0; i <= binary_string.length; i++) {
        if (binary_string[i] != " ") {
          s += binary_string[i];
        }

        if (binary_string[i] == " ") {
          data.push(parseInt(s));
          s = "";
        }
      }

      var arr = new Uint8Array(data);
      var newRom = new NewROM(arr.buffer);

      localforage.setItem("randomizedromfilename", this.rando.filename);
      localforage.setItem("randomizedrom", newRom.getData());
    },

    saveRandomizedRom() {
      localforage.getItem("randomizedrom").then(function(value) {
        if (value == null) {
          this.error = true;
          this.errorMessage = "No randomized ROM found!";
          return;
        }

        localforage.getItem("randomizedromfilename").then(function(value) {
          var rom = new NewROM(value);
          console.log(rom);
          rom.save(value);
        });
      });
    },

    updateFilename(filename) {
      this.baseRomFilename = filename;
    },

    loadDefaults() {
      this.selectedOptions = Object.assign({}, this.defaultOptions);
    },

    onError(error) {
      this.error = true;
      this.errorMessage = error;
    }
  }
};
</script>