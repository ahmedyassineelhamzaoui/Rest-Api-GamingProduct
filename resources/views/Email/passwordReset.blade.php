<style>
    /* Style for the form container */
    .form-container {
      background-color: #f7fafc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 4rem;
    }
  
    /* Styles for the header */
    .form-header h2 {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 1rem;
      text-align: center;
    }
  
    .form-header p {
      font-size: 1rem;
      line-height: 1.5;
      margin-bottom: 2rem;
      text-align: center;
    }
  
    /* Styles for the form */
    form {
      background-color: #ffffff;
      padding: 2rem;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
      border-radius: 0.5rem;
      width: 100%;
      max-width: 30rem;
      display: flex;
      justify-content: center;
    }
  
    form a {
      background-color: #48bb78;
      color: #ffffff;
      border: none;
      padding: 1rem 2rem;
      border-radius: 0.25rem;
      font-size: 1rem;
      font-weight: bold;
      margin-bottom: 1rem;
      width: 100%;
    }
  
    form a:hover {
      background-color: #38a169;
      cursor: pointer;
    }
  
    /* Styles for the message container */
    .message-container {
      margin-top: 2rem;
      text-align: center;
    }
  
    .message-container p {
      font-size: 1rem;
      line-height: 1.5;
      margin-bottom: 0.5rem;
    }
  
    .message-container p:last-child {
      margin-bottom: 0;
    }
  
  </style>
  
  <div class="form-container">
    <div class="form-header">
      <h2>Update your password</h2>
      <p>Hi {{$name}},<br> we received a request that you want to update your password. You can do this by selecting the button below. You’ll be asked to verify your identity, and then you can update your password.</p>
    </div>
  
    <div>
      <form>
        <div>
          <a href="{{'http://localhost:8000/resetPassword?token='.$token}}">Update Password</>
        </div>
      </form>
      <div class="message-container">
        <p>If you didn't make this request, you don't need to do anything.</p>
        <p>Thanks, the {{ config('app.name') }}</p>
      </div>
    </div>
  </div>
  