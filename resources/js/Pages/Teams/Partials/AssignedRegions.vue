<template>
    <div>
        <jet-section-border />
    <jet-form-section @submitted="updateTeamName">

        <template #title>
            Assigned Regions
        </template>

        <template #description>
            Region's this Dispatch Center is allowed to take Requests for Assistance for.
        </template>

        <template #form>
            <div class="col-span-6 mb-4 sm:col-span-4">
                <jet-label for="team" value="Regional Team" />

                <jet-select id="team"
                            class="mt-1 block w-full"
                            v-model="assignedTeam"
                >
                    <option v-for="team in regionalTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </jet-select>

                 <jet-button class="mt-2" @click="assignTeam">
                    Save
                </jet-button>
            </div>

            <div class="col-span-6 sm:col-span-4">
                Currently Assigned

                <div v-for="region in assignedRegions" :key="region.region_id">
                    {{ region.region.name }} 
                    <jet-button class="mt-2" @click="unassignTeam(region.region_id)">
                        Remove
                    </jet-button>
                </div>
            </div>
        </template>
    </jet-form-section>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetSelect from '@/Jetstream/Select'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'

    export default defineComponent({
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSelect,
            JetSectionBorder
        },

        created() {
            axios.get("/api/teams")
                .then((response) => {
                    this.allTeams = response.data;

                    this.regionalTeams = _.filter(response.data, (team) => {
                        return team.type == 1 && team.division_id == this.team.division_id; 
                    });
                });

            axios.get("/api/assigned-regions/" + this.team.id)
                .then((response) => {
                   this.assignedRegions = response.data;
                });
        },

        props: ['team', 'permissions'],

        data() {
            return {
                assignedTeam: null,

                regionalTeams: [],

                assignedRegions: []
            }
        },

        methods: {
            assignTeam() {
                this.$inertia.put(route('assigned-regions.assign'), {
                    'dispatch_center_id': this.team.id,
                    'region_id': this.assignedTeam
                }, {
                    preserveState: false
                })
            },

            unassignTeam(team) {
                this.$inertia.put(route('assigned-regions.unassign'), {
                    'dispatch_center_id': this.team.id,
                    'region_id': team
                }, {
                    preserveState: false
                })
            },

            hasPermission(permission) {
                return this.$page.props.user.permissions.includes(permission) || this.$page.props.user.permissions.includes('*');
            }
        },
    })
</script>
