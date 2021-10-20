<template>
    <jet-form-section @submitted="updateTeamName">
        <template #title>
            Team Name
        </template>

        <template #description>
            The team's name and owner information.
        </template>

        <template #form>
                    <div class="col-span-6 max-w-xl text-sm text-gray-600" v-if="! hasPermission('teams.update')">
                        You don't have permission to core settings for your team. If you believe this is a mistake, contact an Administrator.
                    </div>
            <!-- Team Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />

                <jet-input id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            :disabled="! hasPermission('teams.update')"
                />

                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <template v-if="form.type != 4"> 
             <div class="col-span-6 sm:col-span-4">
                <jet-label for="type" value="Team Type" />

                <jet-select id="type"
                            class="mt-1 block w-full"
                            v-model="form.type"
                            :disabled="! hasPermission('teams.update')"
                >
                    <option value="1">Regional</option>
                    <option value="2">Dispatch Center</option>
                    <option value="3" v-if="team.type == 3 || $page.props.user.current_team.type === 4">Divisional</option>
                </jet-select>

                <jet-input-error :message="form.errors.type" class="mt-2" />
            </div>

            
             <div class="col-span-6 sm:col-span-4" v-if="form.type !== 3">
                <jet-label for="divisionID" value="Division Team" />

                <jet-select id="divisionID"
                            class="mt-1 block w-full"
                            v-model="form.divisionID"
                            :disabled="! hasPermission('teams.update')"
                >
                    <option v-for="team in divisionalTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </jet-select>

                <jet-input-error :message="form.errors.divisionID" class="mt-2" />
            </div>

            
             <div class="col-span-6 sm:col-span-4">
                <jet-label for="nationalID" value="National Team" />

                <jet-select id="nationalID"
                            class="mt-1 block w-full"
                            v-model="form.nationalID"
                            :disabled="! hasPermission('teams.update')"
                >
                    <option v-for="team in nationalTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </jet-select>

                <jet-input-error :message="form.errors.type" class="mt-2" />
            </div>
            </template>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </template>
    </jet-form-section>
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

    export default defineComponent({
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSelect
        },

        created() {
            axios.get("/api/teams")
                .then((response) => {
                    this.allTeams = response.data;

                    this.nationalTeams = _.filter(response.data, (team) => {
                        return team.type === 4;
                    });

                    this.form.nationalID = _.first(this.nationalTeams).id;

                    this.divisionalTeams = _.filter(response.data, (team) => {
                        return team.type === 3;
                    });

                    if(this.$page.props.user.current_team.type <= 2 || this.$page.props.user.current_team.type === 4) {
                        this.divisionalTeams = _.filter(response.data, (team) => {
                            return team.type === 3;
                        });
                    } else {
                        this.divisionalTeams = _.filter(response.data, (team) => {
                            return team.id === this.$page.props.user.current_team.id;
                        });
                    }
                });
        },

        props: ['team', 'permissions'],

        data() {
            return {
                form: this.$inertia.form({
                    name: this.team.name,
                    type: this.team.type,
                    divisionID: this.team.division_id,
                    nationalID: this.team.national_id
                }),

                nationalTeams: [],
                divisionalTeams: []
            }
        },

        methods: {
            updateTeamName() {
                this.form.put(route('teams.update', this.team), {
                    errorBag: 'updateTeamName',
                    preserveScroll: true
                });
            },

            hasPermission(permission) {
                return this.$page.props.user.permissions.includes(permission) || this.$page.props.user.permissions.includes('*');
            }
        },
    })
</script>
