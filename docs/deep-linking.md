# Setting up Deep Linking in Open edX

## Setting up LTI 1.1

1. Setup passpport (can skip if it's already set up)
    1. From the Studio **Settings** menu, select **Advanced Settings**.
    1. In the **LTI Passports** field, place your cursor between the brackets.
    1. Enter the LTI passport string surrounded by quotation marks (you can find passport on the the **Manage LTI 1.1 Connections** page on Oval).
1. Edit the unit in which you want to add the remote LTI tool and select **Advanced** from the **Add New Component** section. Select **LTI Consumer**.
1. Select **Edit** in the component that appears.
1. In the **Edit** window, fill it in following these instructions:

    - **Display Name**: set your preferred name
    - **LTI Version**: LTI 1.1/1.2
    - **LTI ID**: find on the **Manage LTI 1.1 Connections** page on Oval
    - **LTI URL**: https://oval.c3l.ai/api/lti/1.1/launch?resource_type=group_video&resouce_id=1
    - **Request user's username**: True
    - **Request user's email**: True
    - Click the **Save** button.

    > `resource_type` and `resource_id` are used to identify the resource in Oval. Currently, only `group_video` is supported for `resource_type`.

## Setting up LTI 1.3

1. Edit the unit in which you want to add the remote LTI tool and select **Advanced** from the **Add New Component** section. Select **LTI Consumer**.
1. Select **Edit** in the component that appears.
1. In the **Edit** window, fill it in following these instructions:

    - **Display Name**: set your preferred name
    - **LTI Version**: LTI 1.3
    - **Tool Launch URL**: https://oval.c3l.ai/api/lti/1.3/launch?resource_type=group_video&resouce_id=1
    - **Tool Initiate Login URL**: https://oval.c3l.ai/api/lti/1.3/login
    - **Tool Public Key Mode**: Keyset URL
    - **Tool Keyset URL**: https://oval.c3l.ai/api/lti/1.3/jwks
    - **Enable LTI NRPS**: True
    - **Deep linking**: True
    - **Deep Linking Launch URL**: same as **Tool Launch URL**
    - **Request user's username**: True
    - **Request user's email**: True
    - Click the **Save** button.

    > `resource_type` and `resource_id` are used to identify the resource in Oval. Currently, only `group_video` is supported for `resource_type`.
    > URLs can be found in the **Manage LTI Registrations** section on Oval.

1. After saving the content, LTI integration information will be displayed, this needs to be inserted on Oval.
1. Head to the **Manage LTI 1.3 Registrations** on Oval and click on **Add**.
1. Fill in the form using the details from the previous section, and click **Save**.

### Getting user's email

Getting user's email from LTI launch requires some configuration on Open edX which requires admin privileges.

1. Open https://edxstudio.lift.c3l.ai/admin/waffle_utils/waffleflagcourseoverridemodel/
1. Click on **Add Waffle Flag Course Override**.
1. Fill in the form using the following values:

    - **Waffle flag:** lti_consumer.lti_nrps_transmit_pii
    - **Course id:** the course id you want to enable this feature
    - **Override choice:** Force On

    > Course id can be found in the URL of the course page, e.g. id for https://edxstudio.lift.c3l.ai/course/course-v1:UniSA+202+2023 is `course-v1:UniSA+202+2023`

1. Check the **Enabled** checkbox and click **Save**.
1. Open https://edxstudio.lift.c3l.ai/admin/lti_consumer/courseallowpiisharinginltiflag/
1. Click on **Add Course Allow PII Sharing In LTI Flag**.
1. Fill in the form using the following values:

    - **Course id:** the course id you want to enable this feature

1. Check the **Enabled** checkbox and click **Save**.
