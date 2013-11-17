package lib.Kaltura.Notification;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.Map;
import java.util.SortedMap;
import java.util.TreeMap;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * This class is the Notification client.
 * It is responsible for handling a single notification
 */
public class Notification
{
	// Request parameters
	private Map<Integer, Map<String, String>> data = new HashMap<Integer, Map<String, String>>();
	// Whether the request is a multi request
	private boolean multi = false;

	/**
	 * Constructor. Construct a single notification request
	 *
	 * @param params - 
	 * @param validateSignature, boolean: whether or not to validate the signature
	 * @param String, adminSecret: Kaltura admin secret (required if validating signature)
	 */
	public Notification(Map<String, String> params, boolean validateSignature, String adminSecret) {
		if (params.isEmpty()) 
			return;
		
		if(validateSignature && !validateSignature(params, adminSecret))
			throw new NotificationHandlerException("The signature validation failed!", NotificationHandlerException.ERROR_WRONG_SIGNATURE);

		if (params.containsKey("multi_notification") && params.get("multi_notification").equals("true")) {
			this.multi = true;
			this.data = splitMultiNotifications(params);
		} else {
			this.data.put(0, params);
		}
	}
	
	/**
	 * Validates the signature on the parameters in case the request is signed
	 * @param params  - request parameters
	 * @return Whether the request is valid.
	 */
	private boolean validateSignature(Map<String, String> params, String adminSecret) {
		boolean res = false;

		SortedMap<String, String> sortedParams = new TreeMap<String, String>(params);
		StringBuffer sb = new StringBuffer();

		ArrayList<String> validParams = new ArrayList<String>();
		if (sortedParams.containsKey("signed_fields")) {
			String[] validParamsS = sortedParams.get("signed_fields").split(",");
			validParams.addAll(Arrays.asList(validParamsS));
		}
		
		for (Map.Entry<String, String> pair : sortedParams.entrySet()) {
			if (pair.getKey().equals("sig"))
				continue;
			
			if (!validParams.contains(pair.getKey()) && validParams.size() > 1 && !this.multi) {
				if (!pair.getKey().equals("multi_notification")
						&& !pair.getKey().equals("number_of_notifications")) 
					continue;
			}
			
			sb.append(pair.getKey());
			sb.append(pair.getValue());
		}
		
		try {
			
			String beforeHash = adminSecret + sb.toString();
			String calculatedSig = md5Hash(beforeHash);
			res = calculatedSig.equals(params.get("sig"));
			
		} catch (NoSuchAlgorithmException e) {
			throw new NotificationHandlerException("Can'e validate signature. unknown algorithm.", NotificationHandlerException.ERROR_WRONG_SIGNATURE);
		}

		return res;
	}
	
	/**
	 * Calulates the md5hash of a given string
	 * @param str The string we want to calculate its md5
	 * @return The md5 value
	 * @throws NoSuchAlgorithmException
	 */
	private String md5Hash(String str) throws NoSuchAlgorithmException {
		MessageDigest md = MessageDigest.getInstance("MD5");
		md.update(str.getBytes());

		byte byteData[] = md.digest();

		// convert the byte to hex format method 1
		StringBuffer sb = new StringBuffer();
		for (int i = 0; i < byteData.length; i++) {
			sb.append(Integer.toString((byteData[i] & 0xff) + 0x100, 16).substring(1));
		}

		return sb.toString();
	}

	/**
	 * Separates a multi-request notification to its components.
	 * @param params - The multi request parameters
	 * @return The multi-request separated into multiple requests.
	 */
	private Map<Integer, Map<String, String>> splitMultiNotifications(Map<String, String> params){
		Map<Integer, Map<String, String>> multiRequest = new HashMap<Integer, Map<String, String>>();
		
		for (Map.Entry<String, String> mypair : params.entrySet()) {
			String name = mypair.getKey();
			String value = mypair.getValue();
			Pattern p = Pattern.compile("^(not[^_]*)_(.*)$");
			Matcher m = p.matcher(name);

			if (m.find()) {
				String name_num = m.group(1);
				String property = m.group(2);
				Integer num = Integer.valueOf(name_num.replace("not", ""));
				Map<String, String> existingPair = new HashMap<String, String>();
				if (multiRequest.containsKey(num)) {
					existingPair = multiRequest.get(num);
				}
				existingPair.put(property, value);
				multiRequest.put(num, existingPair);
			}
		}
		return multiRequest;
	}

	public Map<Integer, Map<String, String>> getData() {
		return data;
	}
	
}

