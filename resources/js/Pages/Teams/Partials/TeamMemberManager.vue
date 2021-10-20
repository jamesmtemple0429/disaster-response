<template>
    <div>
        <div>
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section @submitted="addTeamMember">
                <template #title>
                    Add Team Member
                </template>

                <template #description>
                    Add a new team member to your team, allowing them to collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the Member ID of the person you would like to add to this team.
                        </div>
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="memberID" value="Member ID" />
                        <jet-input id="memberID" type="text" class="mt-1 block w-full" v-model="addTeamMemberForm.memberID" @blur="searchMember"/>
                        <jet-input-error :message="addTeamMemberForm.errors.memberID" class="mt-2" />
                    </div>

                    <template v-if="searchedMember">
                        <hr class="col-span-6"/>

                        <div class="col-span-6 max-w-xl text-sm text-gray-600" v-if="userNotFound">
                                There is no user found with that Member ID. Please fill out the form below to register the user.
                        </div>
                        
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="firstName" value="First Name" />
                            <jet-input id="firstName" type="text" class="mt-1 block w-full" v-model="addTeamMemberForm.firstName" :disabled="! userNotFound"/>
                            <jet-input-error :message="addTeamMemberForm.errors.firstName" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="lastName" value="Last Name" />
                            <jet-input id="lastName" type="text" class="mt-1 block w-full" v-model="addTeamMemberForm.lastName" :disabled="! userNotFound"/>
                            <jet-input-error :message="addTeamMemberForm.errors.lastName" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="email" value="Email" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="addTeamMemberForm.email" :disabled="! userNotFound" />
                            <jet-input-error :message="addTeamMemberForm.errors.email" class="mt-2" />
                        </div>
                    </template>

                    <!-- Role -->
                    <div class="col-span-6 lg:col-span-4" v-if="availableRoles.length > 0">
                        <jet-label for="roles" value="Role(s)" />
                        <jet-input-error :message="addTeamMemberForm.errors.roles" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                            <span
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                            >
                                                        <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                            @click="onAddMemberClick(role.key)"
                                            v-if="teamRoles.includes(role.key)"
                            >
                                <div :class="{'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-600" :class="{'font-semibold': addTeamMemberForm.roles.includes(role.key)}">
                                            {{ role.name }}
                                        </div>

                                        <svg v-if="addTeamMemberForm.roles.includes(role.key)" class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600 text-left">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                            </span>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <jet-action-message :on="addTeamMemberForm.recentlySuccessful" class="mr-3">
                        Added.
                    </jet-action-message>

                    <jet-button :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing">
                        Add
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <div v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Team Member Invitations -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    Pending Team Invitations
                </template>

                <template #description>
                    These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between" v-for="invitation in team.team_invitations" :key="invitation.id">
                            <div class="text-gray-600">{{ invitation.email }}</div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                                    @click="cancelTeamInvitation(invitation)"
                                                    v-if="userPermissions.canRemoveTeamMembers">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <div v-if="availableUsers.length > 0">
            <jet-section-border />

            <!-- Manage Team Members -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    Team Members
                </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between" v-for="user in availableUsers" :key="user.id">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full" :src="user.profile_photo_url" :alt="user.name">
                                <div class="ml-4">{{ user.name }}</div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button class="ml-2 text-sm text-gray-400 underline"
                                        @click="manageRole(user)"
                                        v-if="userPermissions.canAddTeamMembers && availableRoles.length">
                                    {{ user.role_list }}
                                </button>

                                <div class="ml-2 text-sm text-gray-400" v-else-if="availableRoles.length">
                                    {{ user.role_list }}
                                </div>

                                <!-- Remove Team Member -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500"
                                                    @click="confirmTeamMemberRemoval(user)"
                                                    v-if="userPermissions.canRemoveTeamMembers">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <!-- Role Management Modal -->
        <jet-dialog-modal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <span v-for="(role, i) in availableRoles" :key="role.key">
                        <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                        :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                                        @click="onManageRoleClick(role.key)"
                                        v-if="teamRoles.includes(role.key)">
                            <div>
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600" :class="{'font-semibold': updateRoleForm.role === role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg v-if="updateRoleForm.roles.includes(role.key)" class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                        </span>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="currentlyManagingRole = false">
                    Cancel
                </jet-secondary-button>

                <jet-button class="ml-2" @click="updateRole" :class="{ 'opacity-25': updateRoleForm.processing }" :disabled="updateRoleForm.processing">
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>

        <!-- Remove Team Member Confirmation Modal -->
        <jet-confirmation-modal :show="teamMemberBeingRemoved" @close="teamMemberBeingRemoved = null">
            <template #title>
                Remove Team Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <jet-secondary-button @click="teamMemberBeingRemoved = null">
                    Cancel
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click="removeTeamMember" :class="{ 'opacity-25': removeTeamMemberForm.processing }" :disabled="removeTeamMemberForm.processing">
                    Remove
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetActionSection from '@/Jetstream/ActionSection.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'

    export default defineComponent({
        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },

        props: [
            'team',
            'availableRoles',
            'userPermissions'
        ],

        data() {
            return {
                regionalRoles: [
                    'region-admin',
                    'dro',
                    'region-leader',
                    'editor',
                    'dutyofficer',
                    'responder-sv',
                    'responder'
                ],
                dispatchRoles: [
                    'region-admin',
                'region-leader',
                    'editor',
                    'dispatch-sv',
                    'dispatch'
                ],
                divisionalRoles: [
                    'div-admin',
                    'div-leader'
                ],
                nationalRoles: [
                    'admin'
                ],
                addTeamMemberForm: this.$inertia.form({
                    firstName: '',
                    lastName: '',
                    memberID: '',
                    email: '',
                    roles: [],
                }),

                updateRoleForm: this.$inertia.form({
                    roles: [],
                }),

                leaveTeamForm: this.$inertia.form(),
                removeTeamMemberForm: this.$inertia.form(),

                currentlyManagingRole: false,
                managingRoleFor: null,
                confirmingLeavingTeam: false,
                teamMemberBeingRemoved: null,

                searchedMember: false,
                userNotFound: false
            }
        },

        computed: {
            availableUsers: function() {
                let hiddenRoles = [];

                switch(this.team.type) {
                    case 1:
                        hiddenRoles = ['division-admin','admin'];
                        break;
                    case 2:
                        hiddenRoles = ['division-admin','admin'];
                        break;
                    case 3:
                        hiddenRoles = ['admin'];
                        break;
                }

                return _.filter(this.team.users, (user) => {
                    if(user.role_keys.length === 1) {
                        return ! hiddenRoles.includes(user.role_keys[0]);
                    }

                    return true;
                });
            },

            teamRoles: function () {
                switch(this.team.type) {
                    case 1: 
                        return this.regionalRoles;
                        break;
                    case 2:
                        return this.dispatchRoles;
                        break;
                    case 3:
                        return this.divisionalRoles;
                        break;
                    case 4:
                        return this.nationalRoles;
                        break;
                }      
            }
        },
        methods: {
            searchMember() {
                axios.put(route('members.search'), {
                    'member_id': this.addTeamMemberForm.memberID
                })
                .then((response) => {
                    this.searchedMember = true;

                    if(response.data === "N/A") {
                        this.userNotFound = true;

                        this.addTeamMemberForm.firstName = "";
                        this.addTeamMemberForm.lastName = "";
                        this.addTeamMemberForm.email = "";
                    } else {
                        this.userNotFound = false;

                        this.addTeamMemberForm.firstName = response.data.first_name;
                        this.addTeamMemberForm.lastName = response.data.last_name;
                        this.addTeamMemberForm.email = response.data.email;
                    }
                });
            },

            addTeamMember() {
                this.addTeamMemberForm.post(route('teams.add-new-member', this.team), {
                    errorBag: 'addTeamMember',
                    preserveScroll: true,
                    onSuccess: () => this.addTeamMemberForm.reset(),
                });
            },

            cancelTeamInvitation(invitation) {
                this.$inertia.delete(route('team-invitations.destroy', invitation), {
                    preserveScroll: true
                });
            },

            manageRole(teamMember) {
                this.managingRoleFor = teamMember
                this.updateRoleForm.roles = teamMember.role_keys;
                this.currentlyManagingRole = true
            },

            updateRole() {
                this.updateRoleForm.put(route('teams.member.update', [this.team, this.managingRoleFor]), {
                    preserveScroll: true,
                    onSuccess: () => (this.currentlyManagingRole = false),
                })
            },

            confirmLeavingTeam() {
                this.confirmingLeavingTeam = true
            },

            leaveTeam() {
                this.leaveTeamForm.delete(route('team-members.destroy', [this.team, this.$page.props.user]))
            },

            confirmTeamMemberRemoval(teamMember) {
                this.teamMemberBeingRemoved = teamMember
            },

            removeTeamMember() {
                this.removeTeamMemberForm.delete(route('team-members.destroy', [this.team, this.teamMemberBeingRemoved]), {
                    errorBag: 'removeTeamMember',
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.teamMemberBeingRemoved = null),
                })
            },

            displayableRole(role) {
                return this.availableRoles.find(r => r.key === role).name;
            },

            onAddMemberClick(role) {
                if(this.addTeamMemberForm.roles.includes(role)) {
                    let index = this.addTeamMemberForm.roles.indexOf(role);

                    this.addTeamMemberForm.roles.splice(index, 1);
                } else {
                    this.addTeamMemberForm.roles.push(role);
                }
            },

             onManageRoleClick(role) {
                if(this.updateRoleForm.roles.includes(role)) {
                    let index = this.updateRoleForm.roles.indexOf(role);

                    this.updateRoleForm.roles.splice(index, 1);
                } else {
                    this.updateRoleForm.roles.push(role);
                }
            }
        },
    })
</script>
