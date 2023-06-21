# Setting up Deep Linking in Open edX

1. Edit the unit in which you want to add the remote LTI tool and select **Advanced** from the **Add New Component** section. Select **LTI Consumer**.
1. Select **Edit** in the component that appears.
1. In the "Edit" submenu, fill it in following these instructions:

    - **Display Name**: set your preferred name
    - **LTI Version**: LTI 1.3
    - **Tool Launch URL**: https://oval.c3l.ai/api/lti/launch
    - **Tool Initiate Login URL**: https://oval.c3l.ai/api/lti/login
    - **Tool Public Key Mode**: Keyset URL
    - **Tool Keyset URL**: https://oval.c3l.ai/api/lti/jwks
    - **Deep linking**: True
    - **Deep Linking Launch URL**: https://oval.c3l.ai/api/lti/launch
    - Click the **Save** button.

    > URLs can be found in the **Manage LTI Registrations** section on Oval.

1. After saving the content, LTI integration information will be displayed, this needs to be inserted on Oval.
1. Head to the **Manage LTI Registrations** on Oval and click on **Add**.
1. Fill in the form using the details from the previous section, and click **Save**.
1. Find the group video in database that you want to use for the LTI integration.
1. Update `lti_registration_id` field with the ID of the LTI registration you just created.
