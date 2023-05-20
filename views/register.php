
    <div class="row">
        <div class="form-signin">
            <form method="post">
                <h1 class="h3 mb-3 fw-normal">Create account</h1>
                <div class="form-floating">
                    <input type="email" class="form-control remove-bottom-radius" id="floatingInput" name="login" value="<?php $this->echo('login'); ?>">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control remove-bottom-radius remove-top-radius" id="floatingPassword" name="password">
                    <label for="floatingPassword">Password</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control last-input remove-top-radius" id="floatingPassword" name="repeat">
                    <label for="floatingPassword">Repeat password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
                <p class="mt-5 mb-3 text-muted"><a href="?u=Login" class="link-dark">Or login here</a></p>
            </form>
        </div>
    </div>