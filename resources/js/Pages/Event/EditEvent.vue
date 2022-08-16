<template>
  <div>
    <v-dialog
      v-model="dialog"
      transition="dialog-bottom-transition"
      max-width="600"
    >
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ modalTitle }}</span>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pb-0">
          <v-alert v-if="errors && errors.slot" type="error" class="my-2">
            {{ errors.slot }}
          </v-alert>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  :counter="100"
                  label="Title"
                  required
                  v-model="form.title"
                  :error-messages="errors ? errors.title : null"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  :counter="255"
                  height="75"
                  label="Description"
                  required
                  v-model="form.description"
                  :error-messages="errors ? errors.description : null"
                ></v-textarea>
              </v-col>
              <v-col cols="6">
                <v-datetime-picker
                  label="Start date"
                  :text-field-props="textFieldProps"
                  :date-picker-props="dateProps"
                  :time-picker-props="timeProps"
                  v-model="pickedStartDate"
                  :error-messages="errors ? errors.start_date : null"
                  ref="txtPickedStartDate"
                  readonly
                >
                  <template slot="dateIcon">
                    <v-icon>mdi-calendar</v-icon>
                  </template>
                  <template slot="timeIcon">
                    <v-icon>mdi-clock-outline</v-icon>
                  </template>
                </v-datetime-picker>
              </v-col>
              <v-col cols="6">
                <v-datetime-picker
                  label="End date"
                  :text-field-props="textFieldProps"
                  :date-picker-props="dateProps"
                  :time-picker-props="timeProps"
                  v-model="pickedEndDate"
                  :error-messages="errors ? errors.end_date : null"
                  ref="txtPickedEndDate"
                  readonly
                >
                  <template slot="dateIcon">
                    <v-icon>mdi-calendar</v-icon>
                  </template>
                  <template slot="timeIcon">
                    <v-icon>mdi-clock-outline</v-icon>
                  </template>
                </v-datetime-picker>
              </v-col>
              <v-col cols="12" class="d-flex">
                <label class="v-label theme--light v-label-color"
                  >Color :</label
                >
                <v-color-picker
                  :swatches="swatches"
                  show-swatches
                  hide-canvas
                  hide-inputs
                  hide-sliders
                  v-model="pickedColor"
                ></v-color-picker>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn
            class="ml-auto"
            color="blue-grey"
            outlined
            @click="dialog = false"
          >
            <v-icon left> mdi-close </v-icon>
            Close
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
            v-show="event"
            color="error"
            outlined
            @click="showDeletionModal"
          >
            <v-icon left> mdi-delete </v-icon>
            Delete
          </v-btn>

          <v-btn color="success" outlined @click="saveEvent">
            <v-icon left>
              {{ !event ? "mdi-plus" : "mdi-pencil" }}
            </v-icon>
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDelete" max-width="550px" width="auto">
      <v-card>
        <v-card-title class="text-h5"
          >Are you sure you want to delete this event ?</v-card-title
        >
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialogDelete = false"
            >Cancel</v-btn
          >
          <v-btn color="blue darken-1" text @click="deleteEvent">OK</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import moment from "moment";

const DEFAULT_COLOR = "#2196f3";

export default {
  data() {
    return {
      dialog: false,
      dialogDelete: false,
      pickedColor: DEFAULT_COLOR,
      pickedStartDate: null,
      pickedEndDate: null,
      event: null,
      textFieldProps: {
        appendIcon: "mdi-calendar",
      },
      dateProps: {},
      timeProps: {
        format: "24hr",
        useSeconds: false,
      },
      swatches: [
        ["#2196f3", "#f44336"],
        ["#673ab7", "#4caf50"],
        ["#ff9800", "#757575"],
      ],
      form: this.$inertia.form({
        title: null,
        description: null,
        start_date: null,
        end_date: null,
        color: null,
      }),
      errors: null,
    };
  },
  methods: {
    saveEvent() {
      if (!this.event) {
        this.form.post("/events", {
          onSuccess: () => {
            this.dialog = false;
          },
          onError: () => {
            this.errors = this.$page.props.errors;
          },
        });
      } else {
        this.form.put(`/events/${this.event.id}`, {
          onSuccess: () => {
            this.dialog = false;
          },
          onError: () => {
            this.errors = this.$page.props.errors;
          },
        });
      }
    },
    deleteEvent() {
      this.$inertia.delete(`/events/${this.event.id}`, {
        onSuccess: () => {
          this.dialogDelete = false;
          this.dialog = false;
        },
      });
    },
    showDeletionModal() {
      this.dialogDelete = true;
    },
  },
  watch: {
    dialog: function (val) {
      if (!val) {
        this.pickedStartDate = null;
        this.pickedEndDate = null;
        this.form.reset();
        this.pickedColor = DEFAULT_COLOR;
        this.errors = null;
      }
    },
    pickedColor: function (val) {
      if (val) {
        if (typeof val === "object") {
          this.form.color = val.hex;
        } else {
          this.form.color = val;
        }
      } else {
        this.form.color = null;
      }
    },
    pickedStartDate: function (val) {
      if (val) {
        this.form.start_date = moment(String(val)).format(
          "YYYY-MM-DD HH:mm:ss"
        );
      } else {
        this.$refs.txtPickedStartDate.clearHandler();
        this.form.start_date = null;
      }
    },
    pickedEndDate: function (val) {
      if (val) {
        this.form.end_date = moment(String(val)).format("YYYY-MM-DD HH:mm:ss");
      } else {
        this.$refs.txtPickedEndDate.clearHandler();
        this.form.end_date = null;
      }
    },
  },
  computed: {
    modalTitle: function () {
      return !this.event ? "Add an event" : "Edit the event #" + this.event.id;
    },
  },
  mounted() {
    this.$root.$on("show-editing-modal", (event) => {
      this.dialog = true;
      this.event = event;

      this.form.title = event ? event.name : null;
      this.form.description = event ? event.description : null;
      this.pickedStartDate = event ? moment(event.start).toDate() : null;
      this.pickedEndDate = event ? moment(event.end).toDate() : null;
      this.pickedColor = event ? event.color : DEFAULT_COLOR;
    });
  },
};
</script>

<style scoped>
.v-label-color {
  padding: 8px 0 !important;
}
</style>