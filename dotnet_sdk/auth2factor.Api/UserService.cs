using RestSharp;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace auth2factor.Api
{
    public class UserService
    {
        string API_HOST;
        RestClient client;

        public UserService(string host)
        {
            this.API_HOST = host;
            this.client = new RestClient(host);

            System.Net.ServicePointManager.ServerCertificateValidationCallback +=
            (sender, certificate, chain, sslPolicyErrors) => true;
        }

        public bool Ping(string bearerToken)
        {
            var request = new RestRequest("/v1/users/ping", Method.GET);

            request.AddHeader("Accept", "application/json");
            request.AddHeader("Authorization", "Bearer " + bearerToken);

            // execute the request
            IRestResponse response = client.Execute(request);
            var status = response.StatusCode;


            if (status == System.Net.HttpStatusCode.OK)
            {
                return true;
            }
            else
            {
                return false;
            }

        }

        public Dictionary<String, String> Login(string email, string password, bool doRequestOtc)
        {
            var request = new RestRequest("/v2/users/authenticate", Method.POST);

            request.AddHeader("Accept", "application/json");
            request.AddParameter("email", email);
            request.AddParameter("password", password);
            request.AddParameter("doRequestOtc", doRequestOtc);

            // execute the request
            IRestResponse response = client.Execute(request);
            var status = response.StatusCode;


            var headers = response.Headers;
            Parameter found = headers.SingleOrDefault(x => x.Name.ToUpperCase() == "X-APP-SIGN-REQUEST");
            var result = new Dictionary<String, String>();
            if (found != null)
            {
                result.Add("X-APP-SIGN-REQUEST", (string)found.Value);
            }
            result.Add("status", status.ToString());

            return result;
        }

        public Dictionary<String, String> ValidateOtc(String otcRequestToken, String code)
        {
            var request = new RestRequest("/v2/users/otc", Method.POST);

            request.AddHeader("Accept", "application/json");
            request.AddHeader("Authorization", "Bearer " + otcRequestToken);
            request.AddParameter("code", code);

            // execute the request
            IRestResponse response = client.Execute(request);
            var status = response.StatusCode;


            var headers = response.Headers;
            Parameter found = headers.SingleOrDefault(x => x.Name.ToUpperCase() == "X-APP-BEARER");
            var result = new Dictionary<String, String>();
            if (found != null)
            {
                result.Add("X-APP-BEARER", (string)found.Value);
            }
            result.Add("status", status.ToString());

            return result;
        }
    }
}
