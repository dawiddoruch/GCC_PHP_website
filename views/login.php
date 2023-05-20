
    <div class="row">
        <div class="form-signin">
            <form method="post">
                <h1 class="h3 mb-3 fw-normal">Sign in</h1>
                <div class="form-floating">
                    <input type="email" class="form-control remove-bottom-radius" id="floatingInput" name="login" value="<?php $this->echo('login'); ?>">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control last-input remove-top-radius" id="floatingPassword" name="password">
                    <label for="floatingPassword">Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
                <p class="mt-5 mb-3 text-muted"><a href="?u=Login/register" class="link-dark">Or register here</a></p>
            </form>
        </div>
    </div>